# Charter Depot Website version 1.0.0

These specifications will be finalized on Sunday, July 23, 2023. Revisions made after that date will not be included in 
the first release. *Qui tacet consentire videtur.*

--------------------------------------------------

## URL/Title - To Be Discussed

- [ ] The URL is https://depot.mpinstrument.com.
- [ ] The title of the website is "Depot."

--------------------------------------------------

## Landing Page - To Be Discussed

- [ ] There is no landing/home/welcome page.
- [ ] Unauthenticated users are redirected to the new user registration page.
- [ ] Authenticated users are redirected to the main catalog page.

--------------------------------------------------

## Reports

### Inventory Status - To Be Discussed
 
- [ ] Column 1: Charter item description - searchable, sortable
- [ ] Column 2: Charter key - searchable, sortable
- [ ] Column 3: Charter supplier key, searchable, sortable
- [ ] Column 4: Charter group - sortable
- [ ] Column 5: Manufacturer - sortable
- [ ] Column 6: Location - sortable
- [ ] Column 7: Condition - sortable
- [ ] Column 8: Quantity - sortable
---
- [ ] Filter 1: Dropdown - Group
- [ ] Filter 2: Dropdown - Manufacturer
- [ ] Filter 3: Dropdown - Location
- [ ] Filter 4: Dropdown - Condition
---
- [ ] Exports to Excel spreadsheet
- [ ] Exports to CSV file
---
- [ ] Prints
---
- [ ] Has standard pagination

### Inventory Transactions - To Be Discussed

- [ ] Column 1: Transaction Date - sortable
- [ ] Column 2: Charter item description - searchable, sortable
- [ ] Column 3: Charter key - searchable, sortable
- [ ] Column 4: Charter supplier key - searchable, sortable
- [ ] Column 5: Charter group - sortable
- [ ] Column 6: Manufacturer - sortable
- [ ] Column 7: Location - sortable
- [ ] Column 8: Condition - sortable
- [ ] Column 9: Quantity - sortable
- [ ] Column 10: Reference - searchable, sortable
---
- [ ] Filter 1: Date range - Transaction Date
- [ ] Filter 2: Dropdown - Group
- [ ] Filter 3: Dropdown - Manufacturer
- [ ] Filter 4: Dropdown - Location
- [ ] Filter 5: Dropdown - Condition
---
- [ ] Exports to Excel spreadsheet
- [ ] Exports to CSV file
---
- [ ] Prints
---
- [ ] Has standard pagination

--------------------------------------------------

## Item Catalog

### Catalog Page - To Be Discussed

- [ ] Column 1: Charter item description - sortable, searchable
- [ ] Column 2: Charter key - sortable, searchable
- [ ] Column 3: Charter supplier key - sortable, searchable
- [ ] Column 4: Charter group - sortable
- [ ] Column 5: Manufacturer - sortable
- [ ] Column 6: Location - sortable, hidden if the user is assigned to only one location
- [ ] Column 7: Quantity Available - sortable
---
- [ ] Filter 1: Checkbox - Exclude out of stock items - enabled by default
- [ ] Filter 2: Checkbox - Exclude parts and accessories - enabled by default
- [ ] Filter 3: Dropdown - Group
- [ ] Filter 4: Dropdown - Manufacturer
- [ ] Filter 5: Dropdown - Location - hidden if the user is assigned to only one location
- [ ] Inventory categorized as scrapped or damaged are always excluded from the catalog.
---
- [ ] Exports to Excel spreadsheet
- [ ] Exports to CSV file
---
- [ ] Prints
---
- [ ] Has standard pagination
---
- [ ] Item details page is displayed when a row is clicked

### Item Details Page

#### Item Details Section - To Be Discussed

- [ ] Display Field 1: Charter item description
- [ ] Display Field 2: Charter key
- [ ] Display Field 3: Charter supplier key
- [ ] Display Field 4: Charter group
- [ ] Display Field 5: Location - hidden if the user is assigned to only one location
- [ ] Display Field 6: Quantity available
- [ ] Display Field 7: Item image thumbnail
---
- [ ] Image opens in a new tab when the thumbnail is clicked.

#### Add To Cart Section - To Be Discussed

- [ ] Provides a means to add the item to the shopping cart.
- [ ] Provides a means to add related parts and accessories to the shopping cart.

#### Documents Section - To Be Discussed

- [ ] Lists the titles of all documents associated with the item and its associated parts and accessories
- [ ] Document opens in a new browser tab when the title is clicked.

--------------------------------------------------

## Shopping Cart - To Be Discussed

- [ ] Provides an editable list of items selected for requisition from the catalog.
- [ ] Provides a means to submit an order for items in cart to be delivered.
---
- [ ] Column 1: Charter item description - sortable, searchable
- [ ] Column 2: Quantity Requested - sortable, editable
---
- [ ] Rows are deletable
---
- [ ] Exports to Excel spreadsheet
- [ ] Exports to CSV file
---
- [ ] Prints
---
- [ ] Has standard pagination

--------------------------------------------------

## Order Log - To Be Discussed

- [ ] Provides a status log of previously submitted item requisitions.
---
- [ ] Column 1: Order ID - sortable
- [ ] Column 2: Order Date - sortable
- [ ] Column 2: Ship To Location - sortable
- [ ] Column 2: Order Status - sortable - received, in process, in transit, delivered
---
- [ ] Has standard pagination
---
- [ ] Order details page is displayed when a row is clicked

--------------------------------------------------

## Workflows

### New user verified - To Be Discussed

- [ ] Email notification sent to specified group of users

### Order submitted - To Be Discussed

- [ ] Email notification sent to specified group of users.
- [ ] Email notification sent to user.

### Order status change - To Be Discussed

- [ ] Email notification sent to user.

--------------------------------------------------

## Integrations

### Charter - To Be Discussed

- [ ] Imports uploaded CSV files from Charter and adds/updates item properties appropriately.
---
- [ ] The first row contains column headers and is ignored.
---
- [ ] Column 1: Charter key
- [ ] Column 2: Charter supplier key
- [ ] Column 3: Charter item description
- [ ] Column 4: Charter group

### The Old Tool & Test Set Website - To Be Discussed

- [ ] Imports technical documentation on demand.
- [ ] Imports item images on demand.

### SBT - To Be Discussed

- [ ] Imports inventory control data from SBT hourly.

--------------------------------------------------

## Access Control - To Be Discussed

- [ ] Email address verification is mandatory.
- [ ] Only users with email addresses ending in “@charter.com,” “@completecatv.com,” and "@mpinstrument.com” will be 
able to register.
- [ ] There are two classes of users: Administrators and Charter users
  - [ ] Administrators - Any user with an email address ending in “@completecatv.com” or “@mpinstrument.com”
  - [ ] Charter Users – Everyone else.
    - [ ] Charter users are further limited to viewing data from their assigned locations.
    - [ ] Charter users can be assigned more than one location.

--------------------------------------------------

## Activity Logging - To Be Discussed
- [ ] Each model has a created_by and updated_by nullable bigint property.
- [ ] The created_by property is set to the user's id whenever a model is created.
- [ ] The updated_by property is set to the user's id whenever a model is updated.
