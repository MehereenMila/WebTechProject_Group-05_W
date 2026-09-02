# FoodShare 🍲🤝
 
## ✨ Key Features
*   **MVC Architecture:** Clean, modular, and maintainable codebase separating Model, View, and Controller logic.
*   **Role-Based Access Control:** 
    *   **Admin:** Oversees overall activities, user management, and food listings.
    *   **Manager:** Monitors available food and assigns specific pickup/delivery tasks to volunteers.
    *   **Volunteer:** Receives task assignments, updates delivery statuses, and tracks history.
    *   **Donor:** Creates surplus food listings securely.
*   **Secure Authentication:** Includes OTP-based verification and secure password reset functionalities.
*   **Modern UI/UX:** Clean, intuitive, and responsive interface designed for an optimal user experience (ready for dark-mode enhancements).
 
## 💻 Tech Stack
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%234479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
 
*   **Frontend:** HTML5, CSS3
*   **Backend:** PHP (Core)
*   **Database:** MySQL
*   **Architecture Pattern:** MVC (Model-View-Controller)
 
## 🛠️ Installation & Setup (Local Environment)
1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/MehereenMila/WebTechProject_Group-05_W.git]
    ```
2.  **Server Setup:** Move the project folder to your local server directory (e.g., `htdocs` for XAMPP).
3.  **Database Configuration:**
    *   Open phpMyAdmin and create a new database named `foodshare`.
    *   Import the `.sql` file provided in the repository to set up the tables.
    *   Update your local database credentials in `Model/DatabaseConnection.php`.
4.  **Run the Application:** Open your browser and navigate to `http://localhost:8080/Web_Technology%20Summer%2025-26/FoodShare/`
