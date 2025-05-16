# AgriMarket System Diagrams

This document contains various diagrams illustrating the architecture and data relationships of the AgriMarket e-commerce system.

## Table of Contents
- [Conceptual Diagram](#conceptual-diagram)
- [Entity-Relationship Diagram](#entity-relationship-diagram)
- [Physical Data Model](#physical-data-model)
- [Database Table Visualizations](#database-table-visualizations)
- [System Architecture Diagram](#system-architecture-diagram)
- [User Flow Diagram](#user-flow-diagram)
- [Component Diagram](#component-diagram)
- [Use Case Diagram](#use-case-diagram)
- [Sequence Diagrams](#sequence-diagrams)
- [Deployment Diagram](#deployment-diagram)
- [Context Diagram](#context-diagram)
- [Data Flow Diagram](#data-flow-diagram)

## Conceptual Diagram

```mermaid
graph TD
    User[User] --> |registers as| Farmer[Farmer]
    User --> |shops as| Buyer[Buyer]
    Farmer --> |uploads| Requirements[Requirements Documents]
    Farmer --> |manages| Product[Products]
    Product --> |belongs to| Category[Category]
    Buyer --> |creates| Order[Orders]
    Order --> |contains| OrderItem[Order Items]
    OrderItem --> |references| Product
    Order --> |generates| Transaction[Transactions]
    Order --> |tracks| OrderMovement[Order Movements]
    Buyer --> |adds to| Cart[Cart]
    Cart --> |contains| Product
    Admin[Administrator] --> |manages| Farmer
    Admin --> |reviews| Requirements
    Admin --> |monitors| Order
```

## Entity-Relationship Diagram

```mermaid
erDiagram
    USERS {
        id int PK
        name string
        email string
        password string
        role enum
        phone string
        otp string
        otp_expires_at timestamp
    }
    
    FARMERS {
        id int PK
        user_id int FK
        farm_name string
        location text
        farm_size string
        description text
        contact string
        status enum
        remarks text
        draft_status boolean
    }
    
    REQUIREMENTS {
        id int PK
        name string
        description text
        is_active boolean
        is_mandatory boolean
        order_index int
    }
    
    FARMER_REQUIREMENTS {
        id int PK
        farmer_id int FK
        requirement_id int FK
        status enum
        is_checked boolean
        remarks text
    }
    
    CATEGORIES {
        id int PK
        name string
        description text
    }
    
    PRODUCTS {
        id int PK
        farmer_id int FK
        category_id int FK
        product_name string
        short_description string
        description text
        quantity int
        price double
        status enum
        alert_level int
        code string
        is_published boolean
    }
    
    ORDERS {
        id int PK
        order_number string
        buyer_id int FK
        farmer_id int FK
        phone string
        address text
        payment_method string
        payment_reference string
        status enum
        order_date timestamp
        shipped_date timestamp
        delivery_date timestamp
        total double
        is_received boolean
        remarks text
    }
    
    ORDER_ITEMS {
        id int PK
        order_id int FK
        product_id int FK
        quantity int
        price_per_unit double
        total_price double
        product_name string
        product_description text
    }
    
    ORDER_MOVEMENTS {
        id int PK
        order_id int FK
        status string
        remarks text
    }
    
    CARTS {
        id int PK
        user_id int FK
        product_id int FK
        quantity int
    }
    
    TRANSACTIONS {
        id int PK
        order_id int FK
        amount double
        payment_method string
        status string
        reference_number string
    }
    
    MEDIA {
        id int PK
        model_type string
        model_id int
        collection_name string
        file_name string
        mime_type string
        size int
    }
    
    USERS ||--o{ FARMERS : "has"
    USERS ||--o{ ORDERS : "places"
    FARMERS ||--o{ PRODUCTS : "sells"
    FARMERS ||--o{ FARMER_REQUIREMENTS : "must fulfill"
    REQUIREMENTS ||--o{ FARMER_REQUIREMENTS : "assigned to"
    CATEGORIES ||--o{ PRODUCTS : "categorizes"
    PRODUCTS ||--o{ ORDER_ITEMS : "included in"
    PRODUCTS ||--o{ CARTS : "added to"
    ORDERS ||--o{ ORDER_ITEMS : "contains"
    ORDERS ||--o{ ORDER_MOVEMENTS : "tracks"
    ORDERS ||--o{ TRANSACTIONS : "has"
    FARMERS ||--o{ ORDERS : "receives"
    PRODUCTS ||--o{ MEDIA : "has"
    FARMER_REQUIREMENTS ||--o{ MEDIA : "has"
```

## Physical Data Model

```mermaid
classDiagram
    class users {
        +id: bigint(20) PK
        +name: varchar(255)
        +email: varchar(255)
        +email_verified_at: timestamp
        +password: varchar(255)
        +two_factor_secret: text
        +two_factor_recovery_codes: text
        +remember_token: varchar(100)
        +created_at: timestamp
        +updated_at: timestamp
        +otp: varchar(255)
        +otp_expires_at: timestamp
    }
    
    class farmers {
        +id: bigint(20) PK
        +user_id: bigint(20) FK
        +farm_name: varchar(255)
        +location: text
        +farm_size: varchar(255)
        +description: text
        +contact: varchar(255)
        +status: enum
        +remarks: text
        +created_at: timestamp
        +updated_at: timestamp
        +draft_status: boolean
    }
    
    class requirements {
        +id: bigint(20) PK
        +name: varchar(255)
        +description: text
        +is_active: boolean
        +is_mandatory: boolean
        +order_index: int
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class farmer_requirements {
        +id: bigint(20) PK
        +farmer_id: bigint(20) FK
        +requirement_id: bigint(20) FK
        +status: enum
        +is_checked: boolean
        +remarks: text
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class categories {
        +id: bigint(20) PK
        +name: varchar(255)
        +description: text
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class products {
        +id: bigint(20) PK
        +farmer_id: bigint(20) FK
        +category_id: bigint(20) FK
        +product_name: varchar(255)
        +short_description: varchar(255)
        +description: text
        +quantity: bigint(20)
        +price: double
        +status: enum
        +alert_level: bigint(20)
        +code: varchar(255)
        +is_published: boolean
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class orders {
        +id: bigint(20) PK
        +order_number: varchar(255)
        +buyer_id: bigint(20) FK
        +farmer_id: bigint(20) FK
        +phone: varchar(255)
        +region: varchar(255)
        +province: varchar(255)
        +city_municipality: varchar(255)
        +barangay: varchar(255)
        +street: varchar(255)
        +zip_code: varchar(255)
        +payment_method: varchar(255)
        +payment_reference: varchar(255)
        +status: enum
        +order_date: timestamp
        +shipped_date: timestamp
        +delivery_date: timestamp
        +total: double
        +is_received: boolean
        +remarks: text
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class order_items {
        +id: bigint(20) PK
        +order_id: bigint(20) FK
        +product_id: bigint(20) FK
        +quantity: int
        +price_per_unit: double
        +total_price: double
        +subtotal: double
        +product_name: varchar(255)
        +product_description: text
        +short_description: varchar(255)
        +product_price: double
        +product_quantity: int
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class order_movements {
        +id: bigint(20) PK
        +order_id: bigint(20) FK
        +status: varchar(255)
        +remarks: text
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class carts {
        +id: bigint(20) PK
        +user_id: bigint(20) FK
        +product_id: bigint(20) FK
        +quantity: int
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class transactions {
        +id: bigint(20) PK
        +order_id: bigint(20) FK
        +amount: double
        +payment_method: varchar(255)
        +status: varchar(255)
        +reference_number: varchar(255)
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class media {
        +id: bigint(20) PK
        +model_type: varchar(255)
        +model_id: bigint(20)
        +uuid: varchar(36)
        +collection_name: varchar(255)
        +name: varchar(255)
        +file_name: varchar(255)
        +mime_type: varchar(255)
        +disk: varchar(255)
        +conversions_disk: varchar(255)
        +size: bigint(20)
        +manipulations: json
        +custom_properties: json
        +generated_conversions: json
        +responsive_images: json
        +order_column: int
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    users "1" --> "0..1" farmers
    users "1" --> "*" orders
    farmers "1" --> "*" products
    farmers "1" --> "*" farmer_requirements
    requirements "1" --> "*" farmer_requirements
    categories "1" --> "*" products
    products "1" --> "*" order_items
    products "1" --> "*" carts
    orders "1" --> "*" order_items
    orders "1" --> "*" order_movements
    orders "1" --> "*" transactions
    farmers "1" --> "*" orders
```

## System Architecture Diagram

```mermaid
graph TB
    subgraph "Client Layer"
        WebBrowser[Web Browser]
        MobileApp[Mobile App]
    end
    
    subgraph "Presentation Layer"
        FilamentAdmin[Filament Admin Panel]
        FarmerPortal[Farmer Portal]
        BuyerPortal[Buyer Portal]
    end
    
    subgraph "Application Layer"
        Controllers[Controllers]
        Livewire[Livewire Components]
        Services[Services]
        Jobs[Queue Jobs]
    end
    
    subgraph "Domain Layer"
        Models[Models]
        Observers[Observers]
        Events[Events]
        Listeners[Listeners]
    end
    
    subgraph "Infrastructure Layer"
        Database[(MySQL Database)]
        FileStorage[File Storage]
        Cache[Cache]
        Queue[Queue]
    end
    
    WebBrowser <--> FilamentAdmin
    WebBrowser <--> FarmerPortal
    WebBrowser <--> BuyerPortal
    MobileApp <--> Controllers
    
    FilamentAdmin <--> Livewire
    FarmerPortal <--> Livewire
    BuyerPortal <--> Livewire
    
    Livewire <--> Controllers
    Controllers <--> Services
    Services <--> Models
    Services <--> Jobs
    
    Models <--> Observers
    Models <--> Events
    Events <--> Listeners
    
    Models <--> Database
    Services <--> FileStorage
    Services <--> Cache
    Jobs <--> Queue
```

## User Flow Diagram

```mermaid
graph TD
    Start((Start)) --> UserRegistration[User Registration]
    UserRegistration --> UserType{User Type?}
    
    UserType -->|Farmer| FarmerRegistration[Farmer Registration]
    UserType -->|Buyer| BuyerDashboard[Buyer Dashboard]
    
    FarmerRegistration --> UploadRequirements[Upload Requirements]
    UploadRequirements --> AdminReview[Admin Reviews Requirements]
    AdminReview -->|Approved| FarmerDashboard[Farmer Dashboard]
    AdminReview -->|Rejected| ReviseRequirements[Revise Requirements]
    ReviseRequirements --> UploadRequirements
    
    FarmerDashboard --> ManageProducts[Manage Products]
    ManageProducts --> AddProduct[Add Product]
    ManageProducts --> EditProduct[Edit Product]
    ManageProducts --> DeleteProduct[Delete Product]
    
    FarmerDashboard --> ViewOrders[View Orders]
    ViewOrders --> ProcessOrder[Process Order]
    ProcessOrder --> ShipOrder[Ship Order]
    
    BuyerDashboard --> BrowseProducts[Browse Products]
    BrowseProducts --> ViewProduct[View Product]
    ViewProduct --> AddToCart[Add to Cart]
    AddToCart --> ViewCart[View Cart]
    ViewCart --> Checkout[Checkout]
    Checkout --> ConfirmOrder[Confirm Order]
    ConfirmOrder --> Payment[Payment]
    Payment --> OrderComplete[Order Complete]
    
    BuyerDashboard --> TrackOrders[Track Orders]
    TrackOrders --> ReceiveOrder[Receive Order]
    ReceiveOrder --> RateProduct[Rate Product]
```

## Component Diagram

```mermaid
graph TB
    subgraph "User Management"
        Authentication[Authentication]
        UserRegistration[User Registration]
        UserProfile[User Profile]
        OTPVerification[OTP Verification]
    end
    
    subgraph "Farmer Management"
        FarmerRegistration[Farmer Registration]
        FarmerProfile[Farmer Profile]
        RequirementSystem[Requirement System]
        DocumentUpload[Document Upload]
    end
    
    subgraph "Product Management"
        ProductCatalog[Product Catalog]
        ProductCreation[Product Creation]
        ProductEditing[Product Editing]
        CategoryManagement[Category Management]
        MediaUpload[Media Upload]
    end
    
    subgraph "Order Management"
        Cart[Shopping Cart]
        Checkout[Checkout]
        OrderProcessing[Order Processing]
        OrderTracking[Order Tracking]
        OrderMovements[Order Movements]
    end
    
    subgraph "Payment Management"
        PaymentGateway[Payment Gateway]
        TransactionRecording[Transaction Recording]
        PaymentVerification[Payment Verification]
    end
    
    subgraph "Communication"
        Notifications[Notifications]
        Messaging[Messaging]
        Comments[Comments]
    end
    
    subgraph "Admin Dashboard"
        UserManagement[User Management]
        FarmerApproval[Farmer Approval]
        RequirementReview[Requirement Review]
        OrderMonitoring[Order Monitoring]
        ReportGeneration[Report Generation]
    end
    
    Authentication --> UserRegistration
    UserRegistration --> UserProfile
    UserRegistration --> FarmerRegistration
    FarmerRegistration --> RequirementSystem
    RequirementSystem --> DocumentUpload
    
    FarmerProfile --> ProductCreation
    ProductCreation --> MediaUpload
    ProductCreation --> CategoryManagement
    ProductCreation --> ProductCatalog
    
    ProductCatalog --> Cart
    Cart --> Checkout
    Checkout --> PaymentGateway
    PaymentGateway --> TransactionRecording
    Checkout --> OrderProcessing
    OrderProcessing --> OrderTracking
    OrderTracking --> OrderMovements
    
    OrderProcessing --> Notifications
    Notifications --> Messaging
    ProductCatalog --> Comments
    
    UserManagement --> FarmerApproval
    FarmerApproval --> RequirementReview
    OrderMonitoring --> ReportGeneration
```

## Database Table Visualizations

### Users Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for users |
| name | varchar(255) | NOT NULL | User's full name |
| email | varchar(255) | UNIQUE, NOT NULL | User's email address |
| email_verified_at | timestamp | NULL | When email was verified |
| password | varchar(255) | NOT NULL | Hashed password |
| remember_token | varchar(100) | NULL | Token for "remember me" functionality |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |
| otp | varchar(255) | NULL | One-time password for verification |
| otp_expires_at | timestamp | NULL | OTP expiration time |

### Farmers Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for farmers |
| user_id | bigint(20) | FOREIGN KEY | Reference to users table |
| farm_name | varchar(255) | NULL | Name of the farm |
| location | text | NULL | Farm location details |
| farm_size | varchar(255) | NULL | Size of the farm |
| description | text | NULL | Description of the farm |
| contact | varchar(255) | NULL | Contact information |
| status | enum | DEFAULT 'Pending' | Status of farmer registration |
| remarks | text | NULL | Additional notes |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |
| draft_status | boolean | DEFAULT false | Whether profile is in draft mode |

### Products Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for products |
| farmer_id | bigint(20) | FOREIGN KEY | Reference to farmers table |
| category_id | bigint(20) | FOREIGN KEY, NULL | Reference to categories table |
| product_name | varchar(255) | NULL | Name of the product |
| short_description | varchar(255) | NULL | Brief product description |
| description | text | NULL | Detailed product description |
| quantity | bigint(20) | NULL | Available quantity |
| price | double | NULL | Product price |
| status | enum | DEFAULT 'Available' | Product availability status |
| alert_level | bigint(20) | DEFAULT 20 | Inventory alert threshold |
| code | varchar(255) | UNIQUE, NULL | Product code |
| is_published | boolean | DEFAULT true | Whether product is visible |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Orders Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for orders |
| order_number | varchar(255) | UNIQUE, NULL | Order reference number |
| buyer_id | bigint(20) | FOREIGN KEY | Reference to users table |
| farmer_id | bigint(20) | FOREIGN KEY, NULL | Reference to farmers table |
| phone | varchar(255) | NULL | Contact phone number |
| region | varchar(255) | NULL | Delivery region |
| province | varchar(255) | NULL | Delivery province |
| city_municipality | varchar(255) | NULL | Delivery city/municipality |
| barangay | varchar(255) | NULL | Delivery barangay |
| street | varchar(255) | NULL | Delivery street address |
| zip_code | varchar(255) | NULL | Delivery postal code |
| payment_method | varchar(255) | NULL | Method of payment |
| payment_reference | varchar(255) | NULL | Payment reference number |
| status | enum | DEFAULT 'Processing' | Order status |
| order_date | timestamp | DEFAULT CURRENT_TIMESTAMP | Date order was placed |
| shipped_date | timestamp | NULL | Date order was shipped |
| delivery_date | timestamp | NULL | Date order was delivered |
| total | double | NULL | Total order amount |
| is_received | boolean | DEFAULT false | Whether order was received |
| remarks | text | NULL | Additional notes |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Requirements Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for requirements |
| name | varchar(255) | NOT NULL | Name of the requirement |
| description | text | NULL | Description of the requirement |
| is_active | boolean | DEFAULT true | Whether requirement is active |
| is_mandatory | boolean | DEFAULT true | Whether requirement is mandatory |
| order_index | int | DEFAULT 0 | Display order |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Farmer Requirements Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier |
| farmer_id | bigint(20) | FOREIGN KEY | Reference to farmers table |
| requirement_id | bigint(20) | FOREIGN KEY | Reference to requirements table |
| status | enum | DEFAULT 'Pending' | Status of requirement fulfillment |
| is_checked | boolean | DEFAULT false | Whether requirement has been reviewed |
| remarks | text | NULL | Additional notes |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Order Items Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier |
| order_id | bigint(20) | FOREIGN KEY | Reference to orders table |
| product_id | bigint(20) | FOREIGN KEY | Reference to products table |
| quantity | int | NULL | Quantity ordered |
| price_per_unit | double | NULL | Price per unit |
| total_price | double | NULL | Total price for item |
| subtotal | double | NULL | Subtotal for item |
| product_name | varchar(255) | NULL | Snapshot of product name |
| product_description | text | NULL | Snapshot of product description |
| short_description | varchar(255) | NULL | Snapshot of short description |
| product_price | double | NULL | Snapshot of product price |
| product_quantity | int | NULL | Snapshot of product quantity |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Database Relationship Visualization

```
+---------------+     +---------------+     +---------------+
|    Users      |     |    Farmers    |     | Requirements  |
+---------------+     +---------------+     +---------------+
| id            |<--->| id            |     | id            |
| name          |     | user_id       |<--->| name          |
| email         |     | farm_name     |     | description   |
| password      |     | location      |     | is_active     |
| otp           |     | farm_size     |     | is_mandatory  |
| otp_expires_at|     | description   |     | order_index   |
+---------------+     | contact       |     +---------------+
        ^             | status        |            ^
        |             | remarks       |            |
        |             | draft_status  |            |
        |             +---------------+            |
        |                     ^                   |
        |                     |                   |
        |                     v                   |
+---------------+     +---------------+     +---------------+
|    Orders     |     |   Products    |     | Farmer_Reqs   |
+---------------+     +---------------+     +---------------+
| id            |     | id            |     | id            |
| order_number  |     | farmer_id     |     | farmer_id     |
| buyer_id      |     | category_id   |     | requirement_id|
| farmer_id     |<--->| product_name  |     | status        |
| phone         |     | description   |     | is_checked    |
| address       |     | quantity      |     | remarks       |
| payment_method|     | price         |     +---------------+
| status        |     | status        |            ^
| total         |     | code          |            |
+---------------+     | is_published  |            |
        ^             +---------------+            |
        |                     ^                   |
        |                     |                   |
        |                     v                   v
+---------------+     +---------------+     +---------------+
| Order_Items   |     |    Carts      |     |    Media      |
+---------------+     +---------------+     +---------------+
| id            |     | id            |     | id            |
| order_id      |     | user_id       |     | model_type    |
| product_id    |<--->| product_id    |     | model_id      |
| quantity      |     | quantity      |     | collection_name|
| price_per_unit|     +---------------+     | file_name     |
| total_price   |                           | mime_type     |
| product_name  |                           | size          |
+---------------+                           +---------------+
```

These diagrams and table visualizations provide a comprehensive view of the AgriMarket e-commerce system, showing the relationships between entities, the system architecture, and user flows. The diagrams use the Mermaid syntax for visualization and can be rendered in any Markdown viewer that supports Mermaid diagrams.

## Use Case Diagram

```mermaid
gantt
title Use Case Diagram - AgriMarket System
dateFormat  YYYY-MM-DD
section Farmer
Register as Farmer           :a1, 2025-01-01, 30d
Upload Requirements          :a2, after a1, 30d
Manage Products              :a3, after a2, 30d
Add New Products             :a4, after a3, 15d
Update Product Information   :a5, after a4, 15d
Manage Orders                :a6, after a5, 30d
Process Orders               :a7, after a6, 15d
Ship Orders                  :a8, after a7, 15d

section Buyer
Register as Buyer            :b1, 2025-01-01, 30d
Browse Products              :b2, after b1, 30d
Search Products              :b3, after b2, 15d
Filter Products              :b4, after b3, 15d
Add to Cart                  :b5, after b4, 15d
Checkout                     :b6, after b5, 15d
Make Payment                 :b7, after b6, 15d
Track Orders                 :b8, after b7, 30d
Receive Orders               :b9, after b8, 15d

section Administrator
Manage Users                 :c1, 2025-01-01, 30d
Review Farmer Requirements   :c2, after c1, 30d
Approve/Reject Farmers       :c3, after c2, 15d
Monitor Orders               :c4, after c3, 30d
Generate Reports             :c5, after c4, 15d
```

### ASCII Use Case Diagram

```
+-------------------+    +-------------------+    +-------------------+
|      FARMER       |    |       BUYER       |    |   ADMINISTRATOR   |
+-------------------+    +-------------------+    +-------------------+
| - Register        |    | - Register        |    | - Manage Users    |
| - Upload Docs     |    | - Browse Products |    | - Review Docs     |
| - Manage Products |    | - Search Products |    | - Approve/Reject  |
| - Add Products    |    | - Add to Cart     |    | - Monitor Orders  |
| - Update Products |    | - Checkout        |    | - Generate Reports|
| - Process Orders  |    | - Make Payment    |    +-------------------+
| - Ship Orders     |    | - Track Orders    |
+-------------------+    | - Receive Orders  |
                         +-------------------+
```

## Sequence Diagrams

### Farmer Registration Sequence

```mermaid
sequenceDiagram
    actor User
    participant UI as User Interface
    participant Auth as Authentication Service
    participant FarmerSvc as Farmer Service
    participant ReqSvc as Requirements Service
    participant DB as Database
    participant Email as Email Service
    
    User->>UI: Register as Farmer
    UI->>Auth: Submit Registration
    Auth->>DB: Create User Account
    DB-->>Auth: Account Created
    Auth->>FarmerSvc: Create Farmer Profile
    FarmerSvc->>DB: Store Farmer Information
    DB-->>FarmerSvc: Farmer Created
    FarmerSvc->>ReqSvc: Assign Requirements
    ReqSvc->>DB: Create Farmer Requirements
    DB-->>ReqSvc: Requirements Assigned
    ReqSvc-->>FarmerSvc: Requirements Ready
    FarmerSvc-->>Auth: Farmer Registration Complete
    Auth->>Email: Send Verification Email
    Email-->>Auth: Email Sent
    Auth-->>UI: Registration Successful
    UI-->>User: Show Success Message
```

### ASCII Farmer Registration Sequence

```
User          UI          Auth        FarmerSvc    ReqSvc       DB          Email
 |             |            |            |            |            |            |
 |--Register-->|            |            |            |            |            |
 |             |--Submit--->|            |            |            |            |
 |             |            |--Create--->|            |            |            |
 |             |            |<--Created--|            |            |            |
 |             |            |--Create------------------>|            |            |
 |             |            |            |            |--Store----->|            |
 |             |            |            |            |<--Created---|            |
 |             |            |            |--Assign--->|            |            |
 |             |            |            |            |--Create---->|            |
 |             |            |            |            |<--Assigned--|            |
 |             |            |            |<--Ready----|            |            |
 |             |            |<--Complete-|            |            |            |
 |             |            |--Send------------------------------>|            |
 |             |            |<--Sent------------------------------|            |
 |             |<--Success--|            |            |            |            |
 |<--Success-->|            |            |            |            |            |
 |             |            |            |            |            |            |
```

### Order Processing Sequence

```mermaid
sequenceDiagram
    actor Buyer
    participant UI as Buyer Interface
    participant Cart as Shopping Cart
    participant Order as Order Service
    participant Payment as Payment Service
    participant Notif as Notification Service
    participant DB as Database
    actor Farmer
    
    Buyer->>UI: Checkout Cart
    UI->>Cart: Get Cart Items
    Cart->>DB: Retrieve Cart Data
    DB-->>Cart: Cart Items
    Cart-->>UI: Display Cart Summary
    Buyer->>UI: Confirm Order
    UI->>Order: Create Order
    Order->>DB: Store Order Information
    DB-->>Order: Order Created
    Order->>Payment: Process Payment
    Payment->>DB: Record Transaction
    DB-->>Payment: Transaction Recorded
    Payment-->>Order: Payment Successful
    Order->>Notif: Notify Farmer
    Notif-->>Farmer: New Order Notification
    Order-->>UI: Order Confirmation
    UI-->>Buyer: Show Order Success
```

### ASCII Order Processing Sequence

```
Buyer         UI           Cart         Order        Payment      Notif        DB           Farmer
 |             |             |             |             |             |             |             |
 |--Checkout-->|             |             |             |             |             |             |
 |             |--Get Items-->|             |             |             |             |             |
 |             |             |--Retrieve--->|             |             |             |             |
 |             |             |<--Items------|             |             |             |             |
 |             |<--Summary---|             |             |             |             |             |
 |--Confirm--->|             |             |             |             |             |             |
 |             |--Create-------------------->|             |             |             |             |
 |             |             |             |--Store--------------------->|             |             |
 |             |             |             |<--Created------------------|             |             |
 |             |             |             |--Process---->|             |             |             |
 |             |             |             |             |--Record----->|             |             |
 |             |             |             |             |<--Recorded---|             |             |
 |             |             |             |<--Success---|             |             |             |
 |             |             |             |--Notify-------------------->|             |             |
 |             |             |             |             |             |--Notify-------------------->|
 |             |<--Confirm---|             |             |             |             |             |
 |<--Success-->|             |             |             |             |             |             |
 |             |             |             |             |             |             |             |
```

## Deployment Diagram

```mermaid
flowchart TB
    subgraph "Client Devices"
        Browser["Web Browser"]
        Mobile["Mobile Device"]
    end
    
    subgraph "Web Server"
        Nginx["Nginx Web Server"]
        PHP["PHP 8.x"]
        Laravel["Laravel Framework"]
        Livewire["Livewire Components"]
        Filament["Filament Admin Panel"]
    end
    
    subgraph "Database Server"
        MySQL["MySQL Database"]
    end
    
    subgraph "File Storage"
        S3["AWS S3 / Local Storage"]
    end
    
    subgraph "Services"
        Queue["Laravel Queue"]
        Cache["Redis Cache"]
        Email["SMTP Email Service"]
        SMS["SMS Gateway"]
    end
    
    Browser --> Nginx
    Mobile --> Nginx
    Nginx --> PHP
    PHP --> Laravel
    Laravel --> Livewire
    Laravel --> Filament
    Laravel --> MySQL
    Laravel --> S3
    Laravel --> Queue
    Laravel --> Cache
    Laravel --> Email
    Laravel --> SMS
```

### ASCII Deployment Diagram

```
+------------------+     +------------------+
|  Client Devices  |     |   Web Server    |
+------------------+     +------------------+
| - Web Browser    |---->| - Nginx         |
| - Mobile Device  |     | - PHP 8.x       |
+------------------+     | - Laravel       |----+
                         | - Livewire      |    |
                         | - Filament      |    |
                         +------------------+    |
                                |                |
                                v                v
+------------------+     +------------------+    |    +------------------+
| Database Server  |<----| Services         |<---+----| File Storage     |
+------------------+     +------------------+         +------------------+
| - MySQL Database |     | - Laravel Queue |         | - AWS S3         |
+------------------+     | - Redis Cache   |         | - Local Storage  |
                         | - SMTP Email    |         +------------------+
                         | - SMS Gateway   |
                         +------------------+
```

## Context Diagram

```mermaid
flowchart TD
    AgriMarket(("AgriMarket\nSystem"))
    
    Farmer["Farmer"]
    Buyer["Buyer"]
    Admin["Administrator"]
    PaymentGateway["Payment Gateway"]
    EmailService["Email Service"]
    SMSService["SMS Service"]
    
    Farmer -->|"Register, Manage Products,\nProcess Orders"| AgriMarket
    AgriMarket -->|"Order Notifications,\nRequirement Status"| Farmer
    
    Buyer -->|"Register, Browse Products,\nPlace Orders"| AgriMarket
    AgriMarket -->|"Order Status,\nProduct Information"| Buyer
    
    Admin -->|"Manage System,\nReview Requirements"| AgriMarket
    AgriMarket -->|"System Reports,\nUser Statistics"| Admin
    
    AgriMarket -->|"Payment Requests"| PaymentGateway
    PaymentGateway -->|"Payment Confirmations"| AgriMarket
    
    AgriMarket -->|"Email Notifications"| EmailService
    AgriMarket -->|"SMS Notifications"| SMSService
```

### ASCII Context Diagram

```
                 +--------------------+
                 |                    |
    +----------->|     AgriMarket     |<-----------+
    |            |      System        |            |
    |            |                    |            |
    |            +--------------------+            |
    |                ^    |     ^  |               |
    |                |    |     |  |               |
    |                |    v     |  v               |
+---+----+      +----+---+    ++---+--+      +----+---+
|        |      |        |    |       |      |        |
| Farmer |<---->| Buyer  |    | Admin |      | Payment|
|        |      |        |    |       |      | Gateway|
+--------+      +--------+    +-------+      +--------+
                                   ^
                                   |
                 +------------------+-------------+
                 |                  |             |
             +---+----+        +----+---+     +---+----+
             |        |        |        |     |        |
             | Email  |        |  SMS   |     | Other  |
             | Service|        | Service|     |Services|
             +--------+        +--------+     +--------+
```

## Data Flow Diagram

### Level 0 DFD

```mermaid
flowchart TD
    Farmer["Farmer"] -->|"Registration Data,\nProduct Information"| System(("AgriMarket\nSystem"))
    Buyer["Buyer"] -->|"Registration Data,\nOrder Information"| System
    Admin["Administrator"] -->|"System Configuration"| System
    System -->|"Order Notifications"| Farmer
    System -->|"Order Status,\nProduct Catalog"| Buyer
    System -->|"System Reports"| Admin
```

### ASCII Level 0 DFD

```
+---------+                              +---------+
|         |  Registration Data           |         |
| Farmer  |------------------------------>|         |
|         |  Product Information         |         |
+---------+                              |         |
                                         |         |
+---------+                              |         |
|         |  Registration Data           |         |
| Buyer   |------------------------------>|AgriMarket|
|         |  Order Information           | System  |
+---------+                              |         |
                                         |         |
+---------+                              |         |
|         |  System Configuration        |         |
| Admin   |------------------------------>|         |
|         |                              |         |
+---------+                              +---------+
              Order Notifications          |  |  |
              <-----------------------------+  |  |
                                               |  |
              Order Status, Product Catalog    |  |
              <-------------------------------+  |
                                                  |
              System Reports                      |
              <---------------------------------+
```

### Level 1 DFD

```mermaid
flowchart TD
    Farmer["Farmer"]
    Buyer["Buyer"]
    Admin["Administrator"]
    
    subgraph "AgriMarket System"
        UserMgmt["1.0\nUser Management"]
        ProductMgmt["2.0\nProduct Management"]
        OrderMgmt["3.0\nOrder Management"]
        ReqMgmt["4.0\nRequirement Management"]
        ReportMgmt["5.0\nReporting"]
    end
    
    DB_Users[("Users DB")]
    DB_Products[("Products DB")]
    DB_Orders[("Orders DB")]
    DB_Reqs[("Requirements DB")]
    
    Farmer -->|"Registration Data"| UserMgmt
    Buyer -->|"Registration Data"| UserMgmt
    UserMgmt -->|"Store User Data"| DB_Users
    DB_Users -->|"User Information"| UserMgmt
    
    Farmer -->|"Product Information"| ProductMgmt
    ProductMgmt -->|"Store Product Data"| DB_Products
    DB_Products -->|"Product Information"| ProductMgmt
    ProductMgmt -->|"Product Catalog"| Buyer
    
    Buyer -->|"Order Information"| OrderMgmt
    OrderMgmt -->|"Store Order Data"| DB_Orders
    DB_Orders -->|"Order Information"| OrderMgmt
    OrderMgmt -->|"Order Notifications"| Farmer
    OrderMgmt -->|"Order Status"| Buyer
    
    Farmer -->|"Requirement Documents"| ReqMgmt
    Admin -->|"Review Requirements"| ReqMgmt
    ReqMgmt -->|"Store Requirement Data"| DB_Reqs
    DB_Reqs -->|"Requirement Information"| ReqMgmt
    ReqMgmt -->|"Requirement Status"| Farmer
    
    DB_Users -->|"User Data"| ReportMgmt
    DB_Products -->|"Product Data"| ReportMgmt
    DB_Orders -->|"Order Data"| ReportMgmt
    DB_Reqs -->|"Requirement Data"| ReportMgmt
    ReportMgmt -->|"System Reports"| Admin
```

### ASCII Level 1 DFD

```
+--------+                                                  +--------+
|        |                                                  |        |
| Farmer |                                                  | Buyer  |
|        |                                                  |        |
+---+----+                                                  +----+---+
    |                                                            |
    |    +--------------------------------------------------+    |
    |    |                  AgriMarket System               |    |
    |    |  +----------+    +----------+    +----------+    |    |
    +--->|  |   1.0    |    |   2.0    |    |   3.0    |<---+    |
    |    |  |   User   |<-->|  Product |<-->|  Order   |    |    |
    |    |  |Management|    |Management|    |Management|---------+
    |    |  +-----+----+    +----+-----+    +-----+----+    |
    |    |        |              |                |          |
    |    |        v              v                v          |
    |    |  +----------+    +----------+    +----------+    |
    +--->|  |   4.0    |    |   5.0    |    |          |    |
         |  |Requirement|    | Reporting|<---|  Database|    |
+-----+  |  |Management|    |          |    |  Storage |    |
|     |  |  +----------+    +----------+    +----------+    |
|Admin|  |                                                   |
|     |  +---------------------------------------------------+
+-----+
```

These diagrams provide a comprehensive visualization of the AgriMarket e-commerce system from multiple perspectives, covering all aspects typically required in a capstone project. The diagrams use both Mermaid syntax for visualization (which can be rendered in any Markdown viewer that supports Mermaid diagrams) and ASCII art for direct viewing without special rendering tools.

These diagrams provide a comprehensive visualization of the AgriMarket e-commerce system from multiple perspectives, covering all aspects typically required in a capstone project. The diagrams use the Mermaid syntax for visualization and can be rendered in any Markdown viewer that supports Mermaid diagrams.
