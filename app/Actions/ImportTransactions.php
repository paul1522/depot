<?php

namespace App\Actions;

use App\Helpers\SBT;
use App\Models\Condition;
use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ImportTransactions
{
    use AsAction;

    public string $commandSignature = 'import:transactions';

    public string $commandDescription = 'Import transactions ictran80';

    public function asCommand(Command $command): void
    {
        $this->handle();
    }

    public function handle(): void
    {
        $ictran = DB::connection('basilisk')
            ->table('ictran80')
            ->where('ictran80.loctid', 'like', 'CH/%')
            ->get();

        foreach ($ictran as $tran) {
            $this->handleTran($tran);
        }
    }

    private function handleTran(mixed $tran): void
    {
        /*
         * trantyp applid orgtype - description
         * _R      PO     S         Received on PO #docno
         * PI      IC               Inventory count adjustment
         * PR      IC               Inventory count adjustment
         * _I      SO     C
         * _R      IC     V
         * TI      IC     S         Transfer
         * TR      IC     S         Transfer
         * RR      SO     C
         */

        $itemLocation = $this->getItemLocation($tran);
        if (! $itemLocation) {
            return;
        }
        if ($tran->trantyp == ' R' && $tran->applid == 'PO' && $tran->orgtype == 'S') {
            $this->handleTranRPOS($tran, $itemLocation);
        } elseif ($tran->trantyp == 'PI' && $tran->applid == 'IC' && $tran->orgtype == '') {
            $this->handleTranPIIC($tran, $itemLocation);
        } elseif ($tran->trantyp == 'PR' && $tran->applid == 'IC' && $tran->orgtype == '') {
            $this->handleTranPIIC($tran, $itemLocation);
        } elseif ($tran->trantyp == ' I' && $tran->applid == 'SO' && $tran->orgtype == 'C') {
            $this->handleTranISOC($tran, $itemLocation);
        } elseif ($tran->trantyp == ' R' && $tran->applid == 'IC' && $tran->orgtype == 'V') {
            //
        } elseif ($tran->trantyp == 'TI' && $tran->applid == 'IC' && $tran->orgtype == 'S') {
            $this->handleTranTIICS($tran, $itemLocation);
        } elseif ($tran->trantyp == 'TR' && $tran->applid == 'IC' && $tran->orgtype == 'S') {
            $this->handleTranTIICS($tran, $itemLocation);
        } elseif ($tran->trantyp == 'RR' && $tran->applid == 'SO' && $tran->orgtype == 'C') {
            $this->handleTranRRSOC($tran, $itemLocation);
        } else {
            dd($tran);
        }

    }

    private function handleTranRPOS(mixed $tran, ItemLocation $itemLocation): void
    {
        $key = [
            'sbt_ttranno' => $tran->ttranno,
        ];
        $data = [
            'date' => SBT::date($tran->tdate),
            'sbt_orgno' => $tran->orgno,
            'quantity' => $tran->tqty,
            'description' => 'Received on PO #'.trim($tran->docno),
            'item_location_id' => $itemLocation->id,
        ];

        Transaction::firstOrCreate($key, $data);
    }

    private function handleTranPIIC(mixed $tran, ItemLocation $itemLocation): void
    {
        $key = [
            'sbt_ttranno' => $tran->ttranno,
        ];
        $data = [
            'date' => SBT::date($tran->tdate),
            'sbt_orgno' => $tran->orgno,
            'quantity' => $tran->tqty,
            'description' => 'Inventory count adjustment',
            'item_location_id' => $itemLocation->id,
        ];

        Transaction::firstOrCreate($key, $data);
    }

    private function handleTranRRSOC(mixed $tran, ItemLocation $itemLocation): void
    {
        $key = [
            'sbt_ttranno' => $tran->ttranno,
        ];
        $data = [
            'date' => SBT::date($tran->tdate),
            'sbt_orgno' => $tran->orgno,
            'quantity' => $tran->tqty,
            'description' => 'Returned from SO #'.trim($tran->docno),
            'item_location_id' => $itemLocation->id,
        ];

        Transaction::firstOrCreate($key, $data);
    }

    private function handleTranTIICS(mixed $tran, ItemLocation $itemLocation): void
    {
        $key = [
            'sbt_ttranno' => $tran->ttranno,
        ];
        $data = [
            'date' => SBT::date($tran->tdate),
            'sbt_orgno' => $tran->orgno,
            'quantity' => $tran->tqty,
            'description' => 'Internal Transfer #'.trim($tran->docno),
            'item_location_id' => $itemLocation->id,
        ];

        Transaction::firstOrCreate($key, $data);
    }

    private function handleTranISOC(mixed $tran, ItemLocation $itemLocation): void
    {
        $key = [
            'sbt_ttranno' => $tran->ttranno,
        ];
        switch ($tran->orgno) {
            case 'MPP':
                $description = 'Internal transfer on SO #'.trim($tran->docno);
                break;
            case 'SCRAP':
                $description = 'Scrapped on SO #'.trim($tran->docno);
                break;
            case 'CH/NC':
                $description = 'Shipped on SO #'.trim($tran->docno);
                break;
            default:
                dd($tran);
        }
        $data = [
            'date' => SBT::date($tran->tdate),
            'sbt_orgno' => $tran->orgno,
            'quantity' => $tran->tqty,
            'description' => $description,
            'item_location_id' => $itemLocation->id,
        ];

        Transaction::firstOrCreate($key, $data);
    }

    private function getItemLocation(mixed $tran): ?ItemLocation
    {
        $item = Item::whereSbtItem(SBT::itemPrefix($tran->item))->first();
        if (! $item) {
            return null;
        }
        $suffix = SBT::itemSuffix($tran->item);
        $condition = $suffix ? Condition::whereSbtSuffix($suffix)->first() : Condition::whereNull('sbt_suffix')->first();
        $location = Location::whereSbtLoctid($tran->loctid)->first();

        return ItemLocation::whereItemId($item->id)
            ->whereLocationId($location->id)
            ->whereConditionId($condition->id)
            ->first();
    }
}
