# BYCIG Infrastructure Optimization - Proposal Submission Software


### ✅ Custom PHP files hosted on a Hostinger Apache server

- Embedded **form submission logic** tailored to BYCIG’s specific data validation and business rules.

### ✅ Efficient MySQL Communication

- Designed and implemented **optimized MySQL queries** for fast and scalable data operations using mysqli PHP driver.
### ✅ ReactPHP Integration

- Integrated **ReactPHP**, a non-blocking I/O library, to overcome PHP’s synchronous limitations.
- Enabled asynchronous execution of independent tasks (e.g., logging, analytics, or auxiliary scripts) while I/O bound operations processed in the background.
- Implemented in CRON jobs where a slow API requests are required to populate data (I/O bound and blocking tasks) to ensure maximum use of resources. 
- Achieved **concurrency similar to Python's `asyncio` or JavaScript's `async/await`**, without changing the underlying language.

### ✅ Architecture & Design

- Created a **modular and scalable architecture** for the solution.
- Documented the system using **UML-based diagrams**, designed via **Mermaid.js**, for visual clarity and future maintainability (available at `/software-designs`

---

## Tools & Technologies Used

- **PHP**
- **ReactPHP** – Event loop and asynchronous task handling
- **MySQL** – Optimized relational queries
- **Mermaid.js** – UML-based software architecture diagrams
- **HTML/CSS/JavaScript** – For form rendering and frontend interaction

---

## Conclusion

This project served as a foundational experience in improving real-world fintech infrastructure through **asynchronous design**, **clean architectural patterns**, and **cost-efficient engineering**. It not only solved a direct business need but also introduced modern software design concepts into a legacy environment like WordPress.
