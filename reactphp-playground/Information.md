[text](https://youtu.be/zr1c9-I3rMw)

# 🧠 Understanding Asynchronous Execution in ReactPHP Using JavaScript’s Event Loop

## 📌 Overview

PHP is a synchronous, blocking language by default. This becomes a performance bottleneck when handling tasks that involve external systems (e.g., APIs, databases, or files). To understand how we can introduce non-blocking behavior in PHP, we can first look at **JavaScript’s event loop** — a proven design for handling asynchronous I/O — and then show how **ReactPHP** brings a similar mechanism to PHP.

---

## 🐘 Native PHP and the Problem of Blocking I/O

### 🔧 How PHP Executes Code by Default

- PHP executes instructions **line by line** in a single thread.
- If a function takes time (e.g. an HTTP request), **the entire script waits** for it to finish.
- There's **no built-in way to offload** these long-running tasks without blocking.

### 📉 Real-World Consequences

- **Poor performance** when dealing with external services.
- **Unresponsive systems** (e.g., delayed web servers).
- **Wasted resources** as the processor is idle while waiting for I/O to finish.
- **Sequential bottlenecks** even when tasks could be executed independently.

---

## 🟨 JavaScript’s Event Loop – A Conceptual Model

To grasp how ReactPHP solves this in PHP, let’s first understand how JavaScript handles the same problem efficiently using its **event loop architecture**.

### 🧵 Components of JavaScript’s Runtime Model

1. **Call Stack** – Executes synchronous code line by line.
2. **Web APIs (Browser) / libuv (Node.js)** – Offloads long-running tasks (e.g., `setTimeout`, network requests).
3. **Callback Queue (Task Queue)** – Stores callbacks of resolved async operations.
4. **Event Loop** – A loop that checks if the call stack is empty, and if so, pushes the next callback from the queue into the stack.

---

### 🔄 Example Flow of Execution

```js
console.log('Script start');

setTimeout(() => {
  console.log('Timeout done');
}, 100);

fetch('https://example.com').then(() => {
  console.log('Fetch complete');
});

console.log('Script end');
