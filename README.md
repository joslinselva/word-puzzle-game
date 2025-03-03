# Word Puzzle Game

This project is a Laravel-based word puzzle game designed for students, with an administrative dashboard for management.

## Solution Overview

The application is structured using Laravel's MVC architecture. It includes:

* **Authentication:** Laravel Breeze is used for authentication, providing a simple and robust system for user login and registration.
* **Role-Based Access Control:** Middleware (`CheckRole`) is implemented to restrict access to certain routes based on user roles (admin or student).
* **Puzzle Generation and Submission:** The `PuzzleController` handles the logic for generating puzzles, accepting word submissions, and managing the game state.
* **Admin Dashboard:** The `AdminController` provides a dashboard for administrative tasks.
* **Services:** Services are used to separate business logic from the controllers.
* **Repositories:** Repositories are used to handle database interactions.
* **Leaderboard:** A leaderboard service is used to keep track of the top scores.
* **Testing:** Unit and Feature tests are included to ensure the application's reliability.

This approach was chosen to leverage Laravel's features for rapid development, maintainability, and security, while also ensuring a clear separation of concerns.

## Setup Instructions

1.  **Clone the Repository:**

    ```bash
    git clone [https://github.com/joslinselva/word-puzzle-game.git](https://github.com/joslinselva/word-puzzle-game.git)
    cd word-puzzle-game
    ```

2.  **Install Dependencies:**

    ```bash
    composer install
    npm install
    ```

3.  **Set Up Environment Variables:**

    * Copy `.env.example` to `.env`.
    * Configure your database settings in the `.env` file.

4.  **Generate Application Key:**

    ```bash
    php artisan key:generate
    ```

5.  **Run Migrations:**

    ```bash
    php artisan migrate
    ```

6.  **Compile Assets:**

    ```bash
    npm run dev
    ```

7.  **Seed the Database (Admin User):**

    ```bash
    php artisan db:seed --class=AdminUserSeeder
    ```

8.  **Seed the Database (Student):**

    ```bash
    php artisan db:seed --class=DatabaseSeeder
    ```

9.  **Run the application:**

    ```bash
    php artisan serve
    ```

10.  **Access the application:**

    * Open your web browser and navigate to `http://127.0.0.1:8000`.


## Testing

To run the tests:

```bash
php artisan test


## Usage

Before you can play the word puzzle game, you need to:

* [Log in](http://127.0.0.1:8000/login) if you have an existing account.
* [Register](http://127.0.0.1:8000/register) for a new account if you don't have one.