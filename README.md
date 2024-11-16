### Design Document for Event Management System

---

#### **Project Overview**

The Event Management System is designed to enable users to create, manage, and participate in events. It provides a comprehensive set of features for event organizers and attendees, ensuring a smooth and secure experience. Key functionalities include user authentication, event creation, ticket management, and event feedback.

---

### **Developed Functionalities**

#### **1. Authentication**
- **Description**: Provides user registration and login functionality. Supports multiple user roles: Organizer and Attendee.
- **Key Features**:
  - Registration with Encrypted Data Field:

    The `users` table includes an `encrypted_data` field designed to store sensitive additional information in an encrypted format. Currently, for testing purposes, the user's name and email are encrypted and stored in this field. In the future, this field can be utilized to securely store other sensitive data as needed, ensuring robust data protection.
  - Login with role-based redirection.
  - Logout functionality.

##### **Validations**:
- Registration:
  - Validates required fields: `name`, `email`, `password`.
  - Ensures unique email.
- Login:
  - Validates required fields: `email`, `password`.
  - Role-based redirection (Organizer → Dashboard, Attendee → Home).

---

#### **2. Event Creation**
- **Description**: Enables organizers to create events with multiple ticket types.
- **Key Features**:
  - Create events with details: `title`, `event_date`, `location` `description`, `user_id` etc.
  - Associate multiple ticket types (e.g., VIP, Regular) with each event.

##### **Validations**:
- Validates required fields: `title`, `event_date`, `description`, `location`.
- Ensures `event_date` is not in the past.
- Authorization:
  - Only organizers can create events.
  - Validates ticket types' fields: `name`, `price`, `quantity`.

---

#### **3. Ticket Management**
- **Description**: Allows attendees to purchase tickets for events.
- **Key Features**:
  - Select ticket type and quantity.
  - Handles stock validation for ticket availability.
  - Stores ticket purchase details and payment status.

##### **Validations**:
- Validates `ticket_type_id` and `quantity`.
- Ensures sufficient ticket quantity.
- Checks if the user is authorized to purchase tickets.

---

#### **4. Reporting Dashboard**
- **Description**: Provides organizers with real-time insights into event performance.
- **Key Features**:
  - Displays total events, attendees, ticket sales, and revenue.
  - Supports filtering by date range.
  - Export each event revenue data to Excel.

##### **Validations**:
- Ensures only organizers can access the dashboard.
- Validates date filters for reporting.
- Tests data export to ensure accuracy and performance for large datasets.

#### **Optimized Event Queries**
- **Description**: Efficient handling of large datasets (10k+ events).
- **Features**:
  - Caching for frequent queries (e.g., event listings).
  - Dynamic filtering based on user input.

#### **Ticket Purchase with Fake Payment Simulation**
- **Description**: Allows users to purchase tickets.
- **Features**:
  - Randomized payment outcome (50% success/failure using `rand(0, 1)`).
  - Email notification on successful purchase.

---

### **Frontend Functionality**

#### **1. Upcoming Events Listing**
- **Description**: Display all upcoming events.
- **Features**:
  - Optimized with caching to improve performance.
  - Filters available for:
    - Event Title
    - Location
    - Event Date
  - Download functionality to export events with ticket details to Excel.

#### **2. Event Detail Page**
- **Description**: Provides detailed information about an event.
- **Features**:
  - Displays event details, ticket types, and remaining ticket quantity.
  - Logged-in users can:
    - Purchase tickets (if available).
    - Leave feedback for the event.
  - Post-purchase email notifications with ticket details.

---

### **Written Tests Case For These Functionalities**

#### **1. Authentication Tests**
- **Registration**:
  - Ensures successful registration with valid data.
  - Tests for validation errors (missing/invalid fields).
- **Login**:
  - Tests role-based redirection after login.
  - Validates errors for invalid credentials.

#### **2. Event Creation Tests**
- Validates successful event creation.
- Ensures only authorized users can create events.
- Tests for validation errors (e.g., missing title, past event date).
- Verifies ticket type creation along with the event.

---

### Highlight Points

- **Database Configuration**:
  Ensure your `.env` file has the correct database credentials configured:
  ```
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=your_database_name
  DB_USERNAME=your_database_user
  DB_PASSWORD=your_database_password
  ```

- **Queue Configuration**:
  Set the queue connection to use the database in the `.env` file:
  ```
  QUEUE_CONNECTION=database
  ```
  After setting up, run the migration for the queue table if not already done:
  ```bash
  php artisan queue:table
  php artisan migrate
  ```
  To process queued jobs, use the following command:
  ```bash
  php artisan queue:listen
  ```

- **Session Configuration**:
  Configure the session driver to use the database for enhanced security and scalability:
  ```
  SESSION_DRIVER=database
  ```
  Run the session table migration if not already set up:
  ```bash
  php artisan session:table
  php artisan migrate
  ```

- **Mail Configuration**:
  Add your email service credentials in the `.env` file to enable email functionality. Ensure the following keys are properly configured:
  ```
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.mailtrap.io
  MAIL_PORT=2525
  MAIL_USERNAME=your_username
  MAIL_PASSWORD=your_password
  ```

- **Seeding Dummy Data**:
  To populate the database with dummy data for testing events and users, run the following command:
  ```bash
  php artisan migrate:fresh --seed
  php artisan module:seed
  ```

---

- **Authentication Test Accounts**:
  Use the following credentials for testing authentication:
  - **Organizer**:
    - Email: `org@gmail.com`
    - Password: `1`
  - **Attendee**:
    - Email: `user@gmail.com`
    - Password: `1`

- **Running Test Cases**:
  To execute all test cases, use the following command:
  ```bash
  php artisan test
  ```

---

## Authors

- [@vikaskumar-e1256](https://www.github.com/vikaskumar-e1256)
