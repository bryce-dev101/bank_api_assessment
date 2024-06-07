# Bank API Assessment

## Project Setup

To set up the project, run the following commands:

```sh
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

## Running Tests
To run the tests, use the command:

```sh
./vendor/bin/sail artisan test
```

## Project Report
Although more challenging than I initially thought, I enjoyed this project. There is still more I want to do, and I will continue in my own time.

## Project Workflow
### Step 1 - Project Setup
I started with a brief overview of the PayFast documentation and then set up Laravel. I chose the "sail" installation as it seemed the simplest way to include instructions for setting up the environment and running the tests. I added a few essential packages such as "sanctum", "phpunit", and "breeze":

- Breeze: Quickly set up user authentication.
- Sanctum: Added security.
- phpUnit: For testing.

### Step 2 - Add in DB and Model/Controller
I added a rough idea of the data structure I thought I would need, knowing the database wouldn't be perfect initially.

### Step 3 - DB Cleanup and Seeding
I believe the best way to test if your DB will work is with real data. Therefore, I tested it using seeding.

### Step 4 - Unit Testing
Following a bit of test-driven development, I used unit tests to build and debug the functionality efficiently.

### Step 5 - Add Payment Initialize Functionality
Since everything revolved around making a payment, this was the obvious first step.

### Step 6 - Cleanup
As time was running out, I prioritized cleaning up to avoid creating too much technical debt.

### Step 7 - Payment Requests
To ensure that all communication with PayFast was recorded in an easily accessible manner, I saved as much as I could of what I was sending to PayFast. This information could later be used to spoof payment notifications.

### Step 8 - Payment Notifications
After some cleanup and optimization, I tackled the final part of the project. I built a middleware to secure the open route, ensuring that only PayFast or an internal IP could access it. I created a custom payload to fake the notification from PayFast and adjusted the factories to match the new changes.

### Step 9 - Final Cleanup and Testing
I noticed that my seeder was breaking, so I fixed it and performed a final cleanup and testing.

