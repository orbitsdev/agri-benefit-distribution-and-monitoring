# Agricultural Benefit Distribution and Monitoring System Diagrams

This document contains various diagrams illustrating the architecture and data relationships of the Agricultural Benefit Distribution and Monitoring System.

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
    User[User] --> |registers as| Admin[Administrator]
    User --> |registers as| Member[Member]
    Admin --> |manages| Distribution[Distribution]
    Admin --> |manages| Barangay[Barangay]
    Admin --> |manages| Crop[Crops]
    Admin --> |monitors| Transaction[Transactions]
    Distribution --> |contains| Crop
    Distribution --> |assigned to| Barangay
    Barangay --> |has| BarangayDistribution[Barangay Distribution]
    BarangayDistribution --> |belongs to| Distribution
    BarangayDistribution --> |has| Beneficiary[Beneficiaries]
    Beneficiary --> |receives| CropsToReceive[Crops To Receive]
    CropsToReceive --> |references| Crop
    Member --> |belongs to| Barangay
    Member --> |processes| Beneficiary
    Beneficiary --> |generates| Transaction
    Transaction --> |records| CropsToReceive
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
        barangay_id int FK
        profile_photo_path string
        is_active boolean
    }
    
    BARANGAYS {
        id int PK
        name string
        description text
        is_active boolean
    }
    
    DISTRIBUTIONS {
        id int PK
        title string
        description text
        distribution_date datetime
        venue string
        is_locked boolean
        status enum
        is_disbursed boolean
    }
    
    BARANGAY_DISTRIBUTIONS {
        id int PK
        barangay_id int FK
        distribution_id int FK
        distribution_date datetime
        venue string
        is_disbursed boolean
    }
    
    BENEFICIARIES {
        id int PK
        barangay_distribution_id int FK
        name string
        address text
        contact_number string
        is_approved boolean
    }
    
    CROPS {
        id int PK
        distribution_id int FK
        name string
        original_stocks int
        updated_stocks int
        is_active boolean
    }
    
    CROPS_TO_RECEIVED {
        id int PK
        beneficiary_id int FK
        crop_id int FK
        quantity int
        is_claimed boolean
        claimed_at datetime
    }
    
    TRANSACTIONS {
        id int PK
        beneficiary_id int FK
        action string
        recorder_details json
        beneficiary_details json
        crop_details json
        barangay_distribution_details json
    }
    
    USERS ||--o{ BARANGAYS : "manages"
    USERS }o--|| BARANGAYS : "belongs to"
    DISTRIBUTIONS ||--o{ BARANGAY_DISTRIBUTIONS : "has"
    BARANGAYS ||--o{ BARANGAY_DISTRIBUTIONS : "has"
    BARANGAY_DISTRIBUTIONS ||--o{ BENEFICIARIES : "has"
    DISTRIBUTIONS ||--o{ CROPS : "has"
    BENEFICIARIES ||--o{ CROPS_TO_RECEIVED : "receives"
    CROPS ||--o{ CROPS_TO_RECEIVED : "allocated to"
    BENEFICIARIES ||--o{ TRANSACTIONS : "generates"
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
        +barangay_id: bigint(20) FK
        +role: enum
        +profile_photo_path: text
        +is_active: boolean
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class barangays {
        +id: bigint(20) PK
        +name: varchar(255)
        +description: text
        +is_active: boolean
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class distributions {
        +id: bigint(20) PK
        +title: varchar(255)
        +description: text
        +distribution_date: datetime
        +venue: varchar(255)
        +is_locked: boolean
        +status: enum
        +is_disbursed: boolean
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class barangay_distributions {
        +id: bigint(20) PK
        +barangay_id: bigint(20) FK
        +distribution_id: bigint(20) FK
        +distribution_date: datetime
        +venue: varchar(255)
        +is_disbursed: boolean
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class beneficiaries {
        +id: bigint(20) PK
        +barangay_distribution_id: bigint(20) FK
        +name: varchar(255)
        +address: text
        +contact_number: varchar(255)
        +is_approved: boolean
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class crops {
        +id: bigint(20) PK
        +distribution_id: bigint(20) FK
        +name: varchar(255)
        +original_stocks: int
        +updated_stocks: int
        +is_active: boolean
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class crops_to_received {
        +id: bigint(20) PK
        +beneficiary_id: bigint(20) FK
        +crop_id: bigint(20) FK
        +quantity: int
        +is_claimed: boolean
        +claimed_at: datetime
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    class transactions {
        +id: bigint(20) PK
        +beneficiary_id: bigint(20) FK
        +action: varchar(255)
        +recorder_details: json
        +beneficiary_details: json
        +crop_details: json
        +barangay_distribution_details: json
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    users "1" --> "0..1" barangays
    distributions "1" --> "*" barangay_distributions
    barangays "1" --> "*" barangay_distributions
    barangay_distributions "1" --> "*" beneficiaries
    distributions "1" --> "*" crops
    beneficiaries "1" --> "*" crops_to_received
    crops "1" --> "*" crops_to_received
    beneficiaries "1" --> "*" transactions
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
| barangay_id | bigint(20) | FOREIGN KEY, NULL | Reference to barangays table |
| role | enum | NOT NULL | User role (Admin, Member) |
| profile_photo_path | text | NULL | Path to profile photo |
| is_active | boolean | DEFAULT true | Whether user is active |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Barangays Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for barangays |
| name | varchar(255) | NOT NULL | Name of the barangay |
| description | text | NULL | Description of the barangay |
| is_active | boolean | DEFAULT true | Whether barangay is active |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Distributions Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for distributions |
| title | varchar(255) | NOT NULL | Title of the distribution |
| description | text | NULL | Description of the distribution |
| distribution_date | datetime | NULL | Date of the distribution |
| venue | varchar(255) | NULL | Venue of the distribution |
| is_locked | boolean | DEFAULT false | Whether distribution is locked |
| status | enum | DEFAULT 'Planned' | Status of the distribution |
| is_disbursed | boolean | DEFAULT false | Whether distribution is disbursed |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Barangay Distributions Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for barangay distributions |
| barangay_id | bigint(20) | FOREIGN KEY | Reference to barangays table |
| distribution_id | bigint(20) | FOREIGN KEY | Reference to distributions table |
| distribution_date | datetime | NULL | Date of the distribution for this barangay |
| venue | varchar(255) | NULL | Venue of the distribution for this barangay |
| is_disbursed | boolean | DEFAULT false | Whether distribution is disbursed to this barangay |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Beneficiaries Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for beneficiaries |
| barangay_distribution_id | bigint(20) | FOREIGN KEY | Reference to barangay_distributions table |
| name | varchar(255) | NOT NULL | Name of the beneficiary |
| address | text | NULL | Address of the beneficiary |
| contact_number | varchar(255) | NULL | Contact number of the beneficiary |
| is_approved | boolean | DEFAULT false | Whether beneficiary is approved |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Crops Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for crops |
| distribution_id | bigint(20) | FOREIGN KEY | Reference to distributions table |
| name | varchar(255) | NOT NULL | Name of the crop |
| original_stocks | int | NOT NULL | Original stock quantity |
| updated_stocks | int | NOT NULL | Current stock quantity |
| is_active | boolean | DEFAULT true | Whether crop is active |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Crops To Received Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier |
| beneficiary_id | bigint(20) | FOREIGN KEY | Reference to beneficiaries table |
| crop_id | bigint(20) | FOREIGN KEY | Reference to crops table |
| quantity | int | NOT NULL | Quantity to be received |
| is_claimed | boolean | DEFAULT false | Whether crop has been claimed |
| claimed_at | datetime | NULL | When the crop was claimed |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Transactions Table

| Column Name | Data Type | Constraints | Description |
|------------|-----------|-------------|-------------|
| id | bigint(20) | PRIMARY KEY | Unique identifier for transactions |
| beneficiary_id | bigint(20) | FOREIGN KEY | Reference to beneficiaries table |
| action | varchar(255) | NOT NULL | Action performed (claim, revert) |
| recorder_details | json | NULL | Details of the user who recorded the transaction |
| beneficiary_details | json | NULL | Snapshot of beneficiary details |
| crop_details | json | NULL | Snapshot of crop details |
| barangay_distribution_details | json | NULL | Snapshot of barangay distribution details |
| created_at | timestamp | NULL | Record creation timestamp |
| updated_at | timestamp | NULL | Record update timestamp |

### Database Relationship Visualization

```
+---------------+     +---------------+     +---------------+
|    Users      |     |   Barangays   |     | Distributions |
+---------------+     +---------------+     +---------------+
| id            |<--->| id            |     | id            |
| name          |     | name          |<--->| title         |
| email         |     | description   |     | description   |
| password      |     | is_active     |     | dist_date     |
| barangay_id   |     +---------------+     | venue         |
| role          |            ^             | status        |
| is_active     |            |             | is_locked     |
+---------------+            |             | is_disbursed  |
        ^                    |             +---------------+
        |                    |                     ^        
        |                    |                     |        
        |                    v                     |        
+---------------+     +---------------+     +---------------+
| Transactions  |     | Barangay_Dist |     |    Crops      |
+---------------+     +---------------+     +---------------+
| id            |     | id            |     | id            |
| beneficiary_id|     | barangay_id   |<--->| distribution_id|
| action        |     | distribution_id|    | name          |
| recorder_det  |     | dist_date     |     | original_stocks|
| beneficiary_det|    | venue         |     | updated_stocks|
| crop_det      |     | is_disbursed  |     | is_active     |
+---------------+     +---------------+     +---------------+
        ^                     ^                    ^        
        |                     |                    |        
        |                     v                    |        
        |             +---------------+            |        
        |             | Beneficiaries |            |        
        |             +---------------+            |        
        +------------>| id            |            |        
                      | barangay_dist_id|          |        
                      | name          |            |        
                      | address       |            |        
                      | contact_number|            |        
                      | is_approved   |            |        
                      +---------------+            |        
                              ^                    |        
                              |                    |        
                              v                    v        
                      +---------------+                     
                      | CropsToReceived|                     
                      +---------------+                     
                      | id            |                     
                      | beneficiary_id|                     
                      | crop_id       |                     
                      | quantity      |                     
                      | is_claimed    |                     
                      | claimed_at    |                     
                      +---------------+                     
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
        MemberPortal[Member Portal]
        QRScanner[QR Scanner Interface]
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
    WebBrowser <--> MemberPortal
    WebBrowser <--> QRScanner
    MobileApp <--> Controllers
    
    FilamentAdmin <--> Livewire
    MemberPortal <--> Livewire
    QRScanner <--> Livewire
    
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
    Start((Start)) --> UserLogin[User Login]
    UserLogin --> UserType{User Type?}
    
    UserType -->|Admin| AdminDashboard[Admin Dashboard]
    UserType -->|Member| MemberDashboard[Member Dashboard]
    
    AdminDashboard --> ManageDistributions[Manage Distributions]
    ManageDistributions --> CreateDistribution[Create Distribution]
    ManageDistributions --> AddCrops[Add Crops to Distribution]
    ManageDistributions --> AssignBarangays[Assign Barangays]
    ManageDistributions --> DisburseDistribution[Disburse Distribution]
    ManageDistributions --> CompleteDistribution[Complete Distribution]
    
    AdminDashboard --> ManageBarangays[Manage Barangays]
    ManageBarangays --> CreateBarangay[Create Barangay]
    ManageBarangays --> EditBarangay[Edit Barangay]
    
    AdminDashboard --> ManageBeneficiaries[Manage Beneficiaries]
    ManageBeneficiaries --> AddBeneficiary[Add Beneficiary]
    ManageBeneficiaries --> ApproveBeneficiary[Approve Beneficiary]
    ManageBeneficiaries --> AssignCrops[Assign Crops to Beneficiary]
    
    AdminDashboard --> ViewTransactions[View Transactions]
    AdminDashboard --> GenerateReports[Generate Reports]
    
    MemberDashboard --> ViewDistributions[View Distributions]
    ViewDistributions --> ProcessClaims[Process Claims]
    ProcessClaims --> ScanQRCode[Scan QR Code]
    ScanQRCode --> ConfirmClaim[Confirm Claim]
    ConfirmClaim --> CaptureProof[Capture Proof of Claim]
    CaptureProof --> RecordTransaction[Record Transaction]
    
    MemberDashboard --> ViewBeneficiaries[View Beneficiaries]
    ViewBeneficiaries --> FilterBeneficiaries[Filter Beneficiaries]
    FilterBeneficiaries --> ClaimBenefit[Claim Benefit]
    FilterBeneficiaries --> RevertClaim[Revert Claim]
    
    MemberDashboard --> ViewProgress[View Distribution Progress]
```

## Component Diagram

```mermaid
graph TB
    subgraph "User Management"
        Authentication[Authentication]
        UserRegistration[User Registration]
        UserProfile[User Profile]
        RoleManagement[Role Management]
    end
    
    subgraph "Distribution Management"
        DistributionCreation[Distribution Creation]
        DistributionEditing[Distribution Editing]
        DistributionDisbursement[Distribution Disbursement]
        ProgressTracking[Progress Tracking]
    end
    
    subgraph "Barangay Management"
        BarangayCreation[Barangay Creation]
        BarangayDistributionManagement[Barangay Distribution Management]
        BarangayDisbursement[Barangay Disbursement]
    end
    
    subgraph "Beneficiary Management"
        BeneficiaryRegistration[Beneficiary Registration]
        BeneficiaryApproval[Beneficiary Approval]
        BeneficiaryFiltering[Beneficiary Filtering]
        QRCodeGeneration[QR Code Generation]
    end
    
    subgraph "Crop Management"
        CropCreation[Crop Creation]
        CropInventory[Crop Inventory]
        CropAllocation[Crop Allocation]
    end
    
    subgraph "Claim Processing"
        QRScanner[QR Scanner]
        ClaimVerification[Claim Verification]
        ProofCapture[Proof Capture]
        ClaimRecording[Claim Recording]
    end
    
    subgraph "Transaction Management"
        TransactionRecording[Transaction Recording]
        TransactionHistory[Transaction History]
        TransactionReporting[Transaction Reporting]
    end
    
    subgraph "Dashboard & Reporting"
        AdminDashboard[Admin Dashboard]
        MemberDashboard[Member Dashboard]
        ProgressWidgets[Progress Widgets]
        ExportFunctionality[Export Functionality]
    end
    
    Authentication --> UserRegistration
    UserRegistration --> UserProfile
    UserProfile --> RoleManagement
    
    DistributionCreation --> DistributionEditing
    DistributionEditing --> DistributionDisbursement
    DistributionDisbursement --> ProgressTracking
    
    BarangayCreation --> BarangayDistributionManagement
    BarangayDistributionManagement --> BarangayDisbursement
    
    BeneficiaryRegistration --> BeneficiaryApproval
    BeneficiaryApproval --> BeneficiaryFiltering
    BeneficiaryFiltering --> QRCodeGeneration
    
    CropCreation --> CropInventory
    CropInventory --> CropAllocation
    
    QRScanner --> ClaimVerification
    ClaimVerification --> ProofCapture
    ProofCapture --> ClaimRecording
    
    ClaimRecording --> TransactionRecording
    TransactionRecording --> TransactionHistory
    TransactionHistory --> TransactionReporting
    
    ProgressTracking --> AdminDashboard
    TransactionReporting --> AdminDashboard
    BeneficiaryFiltering --> MemberDashboard
    ClaimRecording --> MemberDashboard
    AdminDashboard --> ProgressWidgets
    MemberDashboard --> ProgressWidgets
    AdminDashboard --> ExportFunctionality
```

## Use Case Diagram

```mermaid
gantt
title Use Case Diagram - Agricultural Benefit Distribution System
dateFormat  YYYY-MM-DD
section Administrator
Manage Distributions           :a1, 2025-01-01, 30d
Manage Crops                   :a2, after a1, 30d
Manage Barangays               :a3, after a2, 30d
Approve Beneficiaries          :a4, after a3, 15d
Monitor Distribution Progress  :a5, after a4, 15d
Generate Reports               :a6, after a5, 30d
View Transaction History       :a7, after a6, 15d

section Member
View Assigned Distributions    :b1, 2025-01-01, 30d
Process Beneficiary Claims     :b2, after b1, 30d
Scan QR Codes                  :b3, after b2, 15d
Capture Proof of Claims        :b4, after b3, 15d
Revert Claims                  :b5, after b4, 15d
View Progress Statistics       :b6, after b5, 15d
Export Barangay Reports        :b7, after b6, 15d

section Beneficiary
Receive QR Code                :c1, 2025-01-01, 30d
Present QR for Scanning        :c2, after c1, 30d
Claim Benefits                 :c3, after c2, 15d
```

### ASCII Use Case Diagram

```
+-------------------+    +-------------------+    +-------------------+
|   ADMINISTRATOR   |    |      MEMBER       |    |    BENEFICIARY    |
+-------------------+    +-------------------+    +-------------------+
| - Manage Distrib. |    | - View Distrib.  |    | - Receive QR Code |
| - Manage Crops    |    | - Process Claims |    | - Present QR Code |
| - Manage Barangays|    | - Scan QR Codes  |    | - Claim Benefits  |
| - Approve Benef.  |    | - Capture Proof  |    +-------------------+
| - Monitor Progress|    | - Revert Claims  |
| - Generate Reports|    | - View Progress  |
| - View History    |    | - Export Reports |
+-------------------+    +-------------------+
```

## Sequence Diagrams

### Benefit Claim Sequence

```mermaid
sequenceDiagram
    actor Beneficiary
    actor Member
    participant UI as Member Interface
    participant Scanner as QR Scanner
    participant Claim as Claim Service
    participant Inventory as Inventory Service
    participant Transaction as Transaction Service
    participant DB as Database
    
    Beneficiary->>Member: Present QR Code
    Member->>UI: Access QR Scanner
    UI->>Scanner: Initialize Scanner
    Scanner-->>UI: Scanner Ready
    Member->>Scanner: Scan Beneficiary QR
    Scanner->>Claim: Verify Beneficiary
    Claim->>DB: Check Beneficiary Status
    DB-->>Claim: Beneficiary Details
    Claim-->>UI: Display Beneficiary Info
    Member->>UI: Confirm Claim
    UI->>Inventory: Process Claim
    Inventory->>DB: Decrease Crop Inventory
    DB-->>Inventory: Inventory Updated
    Inventory->>Transaction: Record Transaction
    Transaction->>DB: Store Transaction Details
    DB-->>Transaction: Transaction Recorded
    Transaction-->>UI: Claim Successful
    UI-->>Member: Show Success Message
    Member-->>Beneficiary: Confirm Benefit Claimed
```

### ASCII Benefit Claim Sequence

```
Beneficiary    Member    UI    Scanner    Claim    Inventory    Transaction    DB
    |            |       |        |         |           |             |          |
    |--Present-->|       |        |         |           |             |          |
    |            |--Access->      |         |           |             |          |
    |            |       |--Init-->|         |           |             |          |
    |            |       |<-Ready--|         |           |             |          |
    |            |--Scan-->|        |         |           |             |          |
    |            |       |--Verify-->|         |           |             |          |
    |            |       |        |--Check---------------------------->|          |
    |            |       |        |<-Details----------------------------|          |
    |            |       |<-Display-|         |           |             |          |
    |            |--Confirm->      |         |           |             |          |
    |            |       |--Process---------->|           |             |          |
    |            |       |        |         |--Decrease---------------->|          |
    |            |       |        |         |<-Updated------------------|          |
    |            |       |        |         |--Record----->|             |          |
    |            |       |        |         |           |--Store-------->|          |
    |            |       |        |         |           |<-Recorded------|          |
    |            |       |<-Success---------|-----------|--------------          |
    |            |<-Success-|      |         |           |             |          |
    |<-Confirm---|       |        |         |           |             |          |
    |            |       |        |         |           |             |          |
```

### Distribution Disbursement Sequence

```mermaid
sequenceDiagram
    actor Admin
    participant UI as Admin Interface
    participant Dist as Distribution Service
    participant Barangay as Barangay Service
    participant Notif as Notification Service
    participant DB as Database
    actor Member
    
    Admin->>UI: Access Distribution
    UI->>Dist: Get Distribution Details
    Dist->>DB: Retrieve Distribution
    DB-->>Dist: Distribution Data
    Dist-->>UI: Display Distribution
    Admin->>UI: Mark as Disbursed
    UI->>Dist: Update Distribution Status
    Dist->>DB: Begin Transaction
    Dist->>DB: Update Parent Distribution
    Dist->>Barangay: Update Barangay Distributions
    Barangay->>DB: Update Child Distributions
    DB-->>Barangay: Children Updated
    Barangay-->>Dist: Barangays Updated
    Dist->>DB: Commit Transaction
    DB-->>Dist: Transaction Complete
    Dist->>Notif: Notify Barangay Members
    Notif-->>Member: Disbursement Notification
    Dist-->>UI: Disbursement Complete
    UI-->>Admin: Show Success Message
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
                         +------------------+
```

## Context Diagram

```mermaid
flowchart TD
    AgriBenefit(("Agricultural\nBenefit System"))
    
    Admin["Administrator"]
    Member["Member"]
    Beneficiary["Beneficiary"]
    EmailService["Email Service"]
    FileStorage["File Storage"]
    
    Admin -->|"Manage Distributions,\nApprove Beneficiaries"| AgriBenefit
    AgriBenefit -->|"Progress Reports,\nSystem Statistics"| Admin
    
    Member -->|"Process Claims,\nScan QR Codes"| AgriBenefit
    AgriBenefit -->|"Beneficiary Lists,\nDistribution Status"| Member
    
    Beneficiary -->|"Present QR Code,\nClaim Benefits"| AgriBenefit
    AgriBenefit -->|"QR Code,\nBenefit Details"| Beneficiary
    
    AgriBenefit -->|"Notifications"| EmailService
    AgriBenefit -->|"Store Documents,\nProof of Claims"| FileStorage
```

### ASCII Context Diagram

```
                 +--------------------+
                 |                    |
    +----------->|    Agricultural    |<-----------+
    |            |   Benefit System   |            |
    |            |                    |            |
    |            +--------------------+            |
    |                ^    |     ^  |               |
    |                |    |     |  |               |
    |                |    v     |  v               |
+---+----+      +----+---+    ++---+--+      +----+---+
|        |      |        |    |       |      |        |
| Admin  |<---->| Member |    | Benef.|      | Email  |
|        |      |        |    |       |      | Service|
+--------+      +--------+    +-------+      +--------+
                                   ^               ^
                                   |               |
                                   |               |
                                   v               |
                              +--------+          |
                              |        |          |
                              | File   |----------+
                              | Storage|
                              +--------+
```

## Data Flow Diagram

### Level 0 DFD

```mermaid
flowchart TD
    Admin["Administrator"] -->|"Distribution Data,\nCrop Information"| System(("Agricultural\nBenefit System"))
    Member["Member"] -->|"Claim Processing,\nQR Scan Data"| System
    Beneficiary["Beneficiary"] -->|"QR Code Presentation"| System
    System -->|"Progress Reports"| Admin
    System -->|"Beneficiary Lists,\nClaim Status"| Member
    System -->|"Benefit Receipt"| Beneficiary
```

### ASCII Level 0 DFD

```
+---------+                              +---------+
|         |  Distribution Data           |         |
| Admin   |------------------------------>|         |
|         |  Crop Information            |         |
+---------+                              |         |
                                         |         |
+---------+                              |         |
|         |  Claim Processing            | Agri.   |
| Member  |------------------------------>| Benefit |
|         |  QR Scan Data                | System  |
+---------+                              |         |
                                         |         |
+---------+                              |         |
|         |  QR Code Presentation        |         |
| Benef.  |------------------------------>|         |
|         |                              |         |
+---------+                              +---------+
              Progress Reports             |  |  |
              <-----------------------------+  |  |
                                               |  |
              Beneficiary Lists, Claim Status  |  |
              <-------------------------------+  |
                                                  |
              Benefit Receipt                     |
              <---------------------------------+
```

### Level 1 DFD

```mermaid
flowchart TD
    Admin["Administrator"]
    Member["Member"]
    Beneficiary["Beneficiary"]
    
    subgraph "Agricultural Benefit System"
        UserMgmt["1.0\nUser Management"]
        DistMgmt["2.0\nDistribution Management"]
        BenefMgmt["3.0\nBeneficiary Management"]
        ClaimMgmt["4.0\nClaim Processing"]
        ReportMgmt["5.0\nReporting"]
    end
    
    DB_Users[("Users DB")]
    DB_Distributions[("Distributions DB")]
    DB_Beneficiaries[("Beneficiaries DB")]
    DB_Claims[("Claims DB")]
    
    Admin -->|"User Data"| UserMgmt
    Member -->|"User Data"| UserMgmt
    UserMgmt -->|"Store User Data"| DB_Users
    DB_Users -->|"User Information"| UserMgmt
    
    Admin -->|"Distribution Data"| DistMgmt
    DistMgmt -->|"Store Distribution Data"| DB_Distributions
    DB_Distributions -->|"Distribution Information"| DistMgmt
    DistMgmt -->|"Distribution Status"| Member
    
    Admin -->|"Beneficiary Information"| BenefMgmt
    BenefMgmt -->|"Store Beneficiary Data"| DB_Beneficiaries
    DB_Beneficiaries -->|"Beneficiary Information"| BenefMgmt
    BenefMgmt -->|"Beneficiary Lists"| Member
    BenefMgmt -->|"QR Code"| Beneficiary
    
    Member -->|"Claim Data"| ClaimMgmt
    Beneficiary -->|"QR Presentation"| ClaimMgmt
    ClaimMgmt -->|"Store Claim Data"| DB_Claims
    DB_Claims -->|"Claim Information"| ClaimMgmt
    ClaimMgmt -->|"Claim Status"| Member
    ClaimMgmt -->|"Benefit Receipt"| Beneficiary
    
    DB_Users -->|"User Data"| ReportMgmt
    DB_Distributions -->|"Distribution Data"| ReportMgmt
    DB_Beneficiaries -->|"Beneficiary Data"| ReportMgmt
    DB_Claims -->|"Claim Data"| ReportMgmt
    ReportMgmt -->|"System Reports"| Admin
    ReportMgmt -->|"Barangay Reports"| Member
```
