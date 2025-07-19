# BYCIG Infrastructure Optimization - Proposal Submission Software

## Overview
As part of my first technical project with the partnered fintech non-profit **BYCIG**, I was tasked with improving the efficiency and scalability of their digital infrastructure, particularly focusing on their **stock exchange proposal submission workflow** within a WordPress environment.

---

## Objectives

- Streamline the **form submission** process for stock exchange proposals.
- Enable **custom validation logic** to meet BYCIG’s compliance needs.
- Eliminate blocking behavior in PHP execution to **increase concurrency**.
- Improve **workflow performance** in their internal **paper trading platform**.
- Maintain a **cost-effective** and maintainable solution within the WordPress ecosystem.

---

## Key Contributions

### ✅ Custom PHP server in Hostinger

- Developed a custom asynchronous-friendly PHP script using ReactPHP
- Embedded **form submission logic** tailored to BYCIG’s specific data validation and business rules.

### ✅ Efficient MySQL Communication

- Designed and implemented **optimized MySQL queries** for fast and scalable data operations.
- Ensured **data integrity and low latency** during high-frequency submission periods.

### ✅ ReactPHP Integration

- Integrated **ReactPHP**, a non-blocking I/O library, to overcome PHP’s synchronous limitations.
- Enabled asynchronous execution of independent tasks (e.g., logging, analytics, or auxiliary scripts) while database operations processed in the background.
- Achieved **concurrency similar to Python's `asyncio` or JavaScript's `async/await`**, without changing the underlying language.

### ✅ Architecture & Design

- Created a **modular and scalable architecture** for the solution.
- Documented the system using **UML-based diagrams**, designed via **Mermaid.js**, for visual clarity and future maintainability.

---

## Tools & Technologies Used

- **PHP (WordPress Theme Development)**
- **ReactPHP** – Event loop and asynchronous task handling
- **MySQL** – Optimized relational queries
- **Mermaid.js** – UML-based software architecture diagrams
- **HTML/CSS/JavaScript** – For form rendering and frontend interaction

---

## Conclusion

This project served as a foundational experience in improving real-world fintech infrastructure through **asynchronous design**, **clean architectural patterns**, and **cost-efficient engineering**. It not only solved a direct business need but also introduced modern software design concepts into a legacy environment like WordPress.
