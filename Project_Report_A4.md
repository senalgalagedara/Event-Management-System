# College Event Management System

**Introduction**  
The College Event Management System (CEMS) is a web-based solution to plan, publish, register, and report on campus events. It supports separate access for administrators and ordinary users, ensuring secure authentication, streamlined event workflows, and easy participation.

**Goals**  
- Provide a simple, secure, and maintainable event platform.  
- Enable role-based management of users and events.  
- Offer clear navigation: Login → Home → Admin/Functionalities → Help.

**Objectives**  
1. Implement login/logout with role-based access (admin, user).  
2. Build a dynamic home page for logged-in users with event registration.  
3. Create an admin panel for users/events CRUD and reports.  
4. Provide a functionalities overview and a help page.  
5. Include all SQL queries and block unauthorized access.

**Default Credentials**  
Ordinary user: **uoc / uoc** (created in SQL bootstrap).

**Tech (no frameworks)**  
Frontend: HTML, CSS, JS. Backend: PHP (mysqli). DB: MySQL.

**Team Split (5)**  
- DB Manager: schema & SQL, data migration.  
- Auth/Security: login, sessions, guards.  
- Admin Panel: users/events CRUD, reports.  
- Event Flow: listings, registration, “My Events”.  
- UI/Help: styling, accessibility, help content.
