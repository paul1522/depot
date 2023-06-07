{{--
# Based on https://github.com/papertank/envoy-deploy
--}}

@setup
    require __DIR__.'/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

    try {
        $dotenv->load();
        $dotenv->required(['DEPLOY_PHP_VERSION', 'DEPLOY_TMP',
            'DEPLOY_SERVER', 'DEPLOY_REPOSITORY', 'DEPLOY_PATH', 'DEPLOY_DATABASE'])->notEmpty();
    } catch ( Exception $e )  {
        echo $e->getMessage();
    }

    $php_ver           = $_ENV['DEPLOY_PHP_VERSION'];
    $tmp               = $_ENV['DEPLOY_TMP'];
    $user              = $_ENV['DEPLOY_USER'] ?? 'envoy';
    $server            = $_ENV['DEPLOY_SERVER'];
    $repo              = $_ENV['DEPLOY_REPOSITORY'];
    $branch            = $_ENV['DEPLOY_BRANCH'] ?? 'main';
    $path              = $_ENV['DEPLOY_PATH'];
    $db_database       = $_ENV['DEPLOY_DATABASE'];
    $env               = $_ENV['DEPLOY_ENV'] ?? 'production';
    $health_url        = $_ENV['DEPLOY_HEALTH_URL'] ?? '';

    if ( substr($path, 0, 1) !== '/' ) {
        throw new Exception('Careful - your deployment path does not begin with /');
    }

    $date        = ( new DateTime )->format('YmdHis');
    $path        = rtrim($path, '/');
    $release     = $path.'/'.$date;
    $tmp_release = $tmp.'/'.$date;
    $db_password = bin2hex(random_bytes(16));
    $php         = "php{$php_ver}";
    $user_server = "{$user}@{$server}";
    $db_database = $env == "production" ? $db_database : "{$db_database}_{$env}"
@endsetup


@servers ( ['web'   => "{$user}@{$server}", 'localhost' => "127.0.0.1" ] )


@story('check-user')
    check-user-task
@endstory

@story('test-locally')
    clone-locally
    build-locally
@endstory

@story('test-remotely')
    clone-remotely
    build-remotely
@endstory

@story('init')
    clone-remotely
    build-remotely
    rsync-remotely
    initialize
@endstory

@story('deploy')
    clone-remotely
    build-remotely
    rsync-remotely
    deployment-links
    deployment-migrate
    deployment-cache
    deployment-finish
    health-check
@endstory

@story('health-check')
    health-check
@endstory

@story('cleanup')
    deployment-cleanup
@endstory

@story('rollback')
    deployment_rollback
    health_check
@endstory

@story('zap')
    deployment-zap
@endstory


@task ('clone-locally', ['on' => 'localhost'])
    echo "Cloning repository into {{ $tmp_release }}"
    mkdir -p {{ $tmp }} || exit 1
    cd {{ $tmp }} || exit 1
    git clone {{ $repo }} --branch={{ $branch }} --depth=1 -q {{ $tmp_release }} || exit 1
    rm -rf {{ $tmp_release }}/.git || exit 1
    cp {{ $tmp_release }}/.env.{{ $env }} {{ $tmp_release }}/.env
    sudo chmod -R a+rwx {{ $tmp_release }}
    echo "Repository cloned"
@endtask

@task ('build-locally', ['on' => 'localhost'])
    echo "Starting build {{ $date }}"
    cd {{ $tmp_release }} || exit 1
    wget -q -O - https://gist.githubusercontent.com/mikemiller891/a4f4bd65a58a9c2091b3b70a8819b31e/raw/78c1094a8b66bb188f78ac0e18aa95c5307b21c9/getcomposer - | bash
    {{ $php }} composer.phar install --no-dev --no-progress
    npm --non-interactive --no-progress install
    npm --non-interactive --no-progress run build
    echo "Build complete"
@endtask

@task ('check-user-task', ['on' => 'web'])
    mkdir -p {{ $tmp }}
    touch {{ $tmp }}/do_not_keep
    rm {{ $tmp }}/do_not_keep
    mkdir -p {{ $path }}
    touch {{ $path }}/do_not_keep
    rm {{ $path }}/do_not_keep
    mysql -e "show databases"
    sudo echo "Envoy user OK"
@endtask

@task ('clone-remotely', ['on' => 'web'])
    echo "Cloning repository into {{ $tmp_release }}"
    mkdir -p {{ $tmp }} || exit 1
    cd {{ $tmp }} || exit 1
    git clone {{ $repo }} --branch={{ $branch }} --depth=1 -q {{ $tmp_release }} || exit 1
    rm -rf {{ $tmp_release }}/.git || exit 1
    echo "Repository cloned"
@endtask

@task ('build-remotely', ['on' => 'web'])
    echo "Starting build {{ $date }}"
    cd {{ $tmp_release }} || exit 1
    wget -q -O - https://gist.githubusercontent.com/mikemiller891/a4f4bd65a58a9c2091b3b70a8819b31e/raw/78c1094a8b66bb188f78ac0e18aa95c5307b21c9/getcomposer - | bash
    {{ $php }} composer.phar install --no-dev
    export NVM_DIR="$HOME/.nvm"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
    npm --non-interactive --no-progress install
    npm --non-interactive --no-progress run build
    echo "Build complete"
@endtask

@task ('rsync-remotely', ['on' => 'web'])
    echo "Moving build to {{ $path }}"
    #rsync -a {{ $tmp_release }} {{ $path }}
    #rm -rf {{ $tmp_release }}
    mkdir -p {{ $path }}
    mv -v {{ $tmp_release }} {{ $path }}
    echo "Upload complete"
@endtask

@task ('initialize', ['on' => 'web'])
    if [ ! -d {{ $path }}/storage ]; then
        cd {{ $release }}
        mv {{ $release }}/.env.{{ $env }} {{ $release }}/.env
        echo "" >> {{ $release }}/.env
        echo "DB_DATABASE=\"{{ $db_database }}\"" >> {{ $release }}/.env
        echo "DB_USERNAME=\"{{ $db_database }}\"" >> {{ $release }}/.env
        echo "DB_PASSWORD=\"{{ $db_password }}\"" >> {{ $release }}/.env
        echo "" >> {{ $release }}/.env

        {{ $php }} artisan key:generate
        mv {{ $release }}/.env {{ $path }}/.env
        echo "Environment file set up"

        mysql -e "create database \`{{ $db_database }}\`"
        mysql -e "create user '{{ $db_database }}'@'localhost' identified by '{{ $db_password }}'"
        mysql -e "grant all privileges on \`{{$db_database}}\`.* to '{{ $db_database }}'@'localhost'"
        echo "Database initialized"

        cd {{ $path }}
        mv {{ $release }}/storage {{ $path }}/storage
        echo "Storage directory set up"

        rm -rf {{ $release }}

        mkdir -p {{ $release }}/public
        wget -O "{{ $release }}/public/index.php" https://raw.githubusercontent.com/mikemiller891/unlanding/main/index.php
        ln -nfs {{ $release }} {{ $path }}/current
        echo "Placeholder page installed"

        sudo systemctl reload {{ $php }}-fpm
        sudo systemctl reload apache2

        echo "Deployment path initialized"
    else
        echo "Deployment path already initialized (storage symlink exists)!"
    fi
@endtask

@task('deployment-links', ['on' => 'web'])
    cd {{ $path }}
    rm -rf {{ $release }}/storage
    ln -s {{ $path }}/storage {{ $release }}/storage
    ln -s {{ $path }}/storage/app/public {{ $release }}/public/storage
    echo {{$date}} > {{ $path }}/storage/app/build.dat
    echo "Storage directories set up"

    ln -s {{ $path }}/.env {{ $release }}/.env
    echo "Environment file set up"
@endtask

@task('deployment-migrate', ['on' => 'web'])
    {{ $php }} {{ $release }}/artisan migrate --force --no-interaction
    echo "Database migration complete"
@endtask

@task('deployment-cache', ['on' => 'web'])
    {{ $php }} {{ $release }}/artisan view:clear --quiet
    {{ $php }} {{ $release }}/artisan cache:clear --quiet
    {{ $php }} {{ $release }}/artisan config:clear --quiet
    {{ $php }} {{ $release }}/artisan route:clear --quiet
    echo 'Cache cleared'
@endtask

@task('deployment-finish', ['on' => 'web'])
    sudo chown {{ $user }}.www-data {{ $path }}/.env
    sudo chown -R {{ $user }}.www-data {{ $release }}
    sudo chown -R {{ $user }}.www-data {{ $path }}/storage {{ $release }}/bootstrap/cache

    sudo chmod ug=rw,o-rwx {{ $path }}/.env
    sudo chmod -R u+rwX,g+rX-w,o-rwx {{ $release }}
    sudo chmod -R ug+rwX,o-rwx  {{ $path }}/storage
    sudo chmod -R ug+rwX,o-rwx {{ $release }}/bootstrap/cache

    ln -nfs {{ $release }} {{ $path }}/current

    sudo systemctl reload {{ $php }}-fpm
    sudo systemctl reload apache2

    echo "Deployment ({{ $date }}) finished"
@endtask

@task('deployment-cleanup', ['on' => 'web'])
    cd {{ $path }}
    find . -maxdepth 1 -name "20*" -mmin +2880 | head -n 5 | xargs rm -Rf
    echo "Cleaned up old deployments"
@endtask

@task('deployment_rollback', ['on' => 'web'])
    cd {{ $path }}
    ln -nfs {{ $path }}/$(find . -maxdepth 1 -name "20*" | sort  | tail -n 2 | head -n1) {{ $path }}/current
    echo "Rolled back to $(find . -maxdepth 1 -name "20*" | sort  | tail -n 2 | head -n1)"
@endtask

@task('deployment-zap', ['on' => 'web'])
    rm -rf {{ $tmp }}
    echo "Removed {{ $tmp }}"
    rm -rf {{ $path }}
    echo "Removed {{ $path }}"
    mysql -e "drop database \`{{ $db_database }}\`"
    echo "Dropped database {{ $db_database }}"
    mysql -e "drop user '{{ $db_database }}'@'localhost'"
    echo "Dropped user {{ $db_database }}@localhost"
    echo "Zap successful"
@endtask

{{--
TODO: Check that response matches this string.
{"status":"OK","log":{"status":"OK"},"database":{"status":"OK"},"env":{"status":"OK"}}
--}}
@task('health-check', ['on' => 'localhost'])
    @if ( ! empty($health_url) )
        HEALTH="$(curl --write-out "%{http_code}" --silent --output /dev/null {{ $health_url }})"
        if [ "$HEALTH" = "200" ]; then
        printf "\033[0;32mHealth check to {{ $health_url }} OK\033[0m\n"
        else
        printf "\033[1;31mHealth check to {{ $health_url }} FAILED\033[0m\n"
        fi
    @else
        echo "No health check set"
    @endif
@endtask
