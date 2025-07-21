# 🫡 UML Diagram
In Software Engineering, UML or Unified Modelling Language is a standardized graphical language that allows developers to visualize the structure and behaviour of software systems, making it easier for a developers to implement their software systems in code. There are multiple different types of visualizations for a developer to choose.

The proposal submission workflow is documented using a UML Activity Graph

## Image:

[![](https://img.plantuml.biz/plantuml/svg/TLHDJnin4BtlhvWRYKJX3ZGbecgBr8-KmA57htUIjUAr5tyGzj_tZJU1a1OfYX9xvirxRsPy7PCAANUsgf99bcaHVE-ZihJCTMTYDDxHqg_ILWMkgX9TEP-OBAzIHJItbbQSTClnbFXPSvzevGF5vFM6velRK_9Qvq1RarfIX9IhFFinB7z_fyZXYSFLUPwHSesSTqDyjAQSBBujg0a60MSLurQALcFfODQjrBvlMMfjSDTmH0Lm2TbHbsqoFSGa5JUHhkgPzWuS0hjaXwlpUeRa78rRIyqxLZgXcbGzYSHFY2BhVK_9axBMRu7lFjGMWYI5drdd15_UvGLN705Kl1FPnX6-Og59MZbY77AWcgM-YeFJHJ9im__ztxE21FBpyQIUxVIW4syPrGCvbJAEjws1REIyIQ4IUK2S2lH3p90td4gyEggHr0OjzcuDch1XJELJYXvIUA2jpxQXniIGqV3yl8Ey5vdowNeVaeAHDMkL8vD9X1xfO6fqgeMdUzl7jgN0AY7eq5DfRK6z-283HglWEqpGE5dyd3Wup2b6J0HCvii_Fs-c8k6o91rrf7YPPolZN9lhC-DFOl5yZwPR1R45xIM-C4EJe9Zwu9zC8yCeDoyao_YfjJ9kPFgL4scR8yXXGnGIuZ_i9BbegouMlizMWUX88t34w4PnN9n1K7Tv4FGM-9te1smL-2MftE-NkU6aZ8txwoYxdGxkTn3ZGj9ayMjvFqNmwFjySPF7dNvz4eBmiGOebqV33vCbePZx4NjDndtekFDXmEZZcH2fGS5gR0Lg7ZRSh6WYOz5oSr3WqrGQu6WoS9oEBjznOmBhL3OKwnG2sFK07Lj_yTaB8cdVuHKGRloOEO8gMupYnFaHvsN0IjAjW_nqu9bAwh-JTTJpNR8EtCdBKTnwPRyJBJX7C2zZW1zcXQVLzzKrVkCn_Wi0)](https://editor.plantuml.com/uml/TLHDJnin4BtlhvWRYKJX3ZGbecgBr8-KmA57htUIjUAr5tyGzj_tZJU1a1OfYX9xvirxRsPy7PCAANUsgf99bcaHVE-ZihJCTMTYDDxHqg_ILWMkgX9TEP-OBAzIHJItbbQSTClnbFXPSvzevGF5vFM6velRK_9Qvq1RarfIX9IhFFinB7z_fyZXYSFLUPwHSesSTqDyjAQSBBujg0a60MSLurQALcFfODQjrBvlMMfjSDTmH0Lm2TbHbsqoFSGa5JUHhkgPzWuS0hjaXwlpUeRa78rRIyqxLZgXcbGzYSHFY2BhVK_9axBMRu7lFjGMWYI5drdd15_UvGLN705Kl1FPnX6-Og59MZbY77AWcgM-YeFJHJ9im__ztxE21FBpyQIUxVIW4syPrGCvbJAEjws1REIyIQ4IUK2S2lH3p90td4gyEggHr0OjzcuDch1XJELJYXvIUA2jpxQXniIGqV3yl8Ey5vdowNeVaeAHDMkL8vD9X1xfO6fqgeMdUzl7jgN0AY7eq5DfRK6z-283HglWEqpGE5dyd3Wup2b6J0HCvii_Fs-c8k6o91rrf7YPPolZN9lhC-DFOl5yZwPR1R45xIM-C4EJe9Zwu9zC8yCeDoyao_YfjJ9kPFgL4scR8yXXGnGIuZ_i9BbegouMlizMWUX88t34w4PnN9n1K7Tv4FGM-9te1smL-2MftE-NkU6aZ8txwoYxdGxkTn3ZGj9ayMjvFqNmwFjySPF7dNvz4eBmiGOebqV33vCbePZx4NjDndtekFDXmEZZcH2fGS5gR0Lg7ZRSh6WYOz5oSr3WqrGQu6WoS9oEBjznOmBhL3OKwnG2sFK07Lj_yTaB8cdVuHKGRloOEO8gMupYnFaHvsN0IjAjW_nqu9bAwh-JTTJpNR8EtCdBKTnwPRyJBJX7C2zZW1zcXQVLzzKrVkCn_Wi0)

## Code:
```plantuml
@startuml

title Proposal Submission Software

start
note left
  All fetches, except for stock API, occur with a 
  <u>MySQL server</u> and <u>mysqli</u> PHP driver.
end note

note right
  The fork nodes will run multiple tasks <b>concurrently</b>
  using <u>ReactPHP</u>'s event loop to allow non-blocking execution.
end note

note right
    An action within this node can either be <u>async</u> or <u>sync</u>.
    <b>Async</b> tasks are non-blocking by nature while <b>sync</b> task block execution. 
    Usually, <b>async</b> tasks take longer to execute, so they would disrupt ux. 
    This is important because it describes how ReactPHP will treat it.
end note

fork
    :Fetch from a stock external API <b>(ASYNC)</b>;
    note right
        <u>finnhub.io's</u> stock exchange API 
        will be the provider.
    end note
fork again
    :Get cluster leaders <b>(ASYNC)</b>;
end fork

:Populate stock results to PHP form;
:Populate cluster leader results to PHP form;
:User enters proposal details;
:User submits proposal to PHP server (POST);
:Fetch APCu for submission rate limit;

note right
  <u>APCu</u> is an in-memory cache storage in PHP.
end note

if (Reached rate limit?) then (yes)
    :Redirect to an error page;
    :Do not complete request;
else (no)
    fork
        :Insert proposal data <b>(ASYNC)</b>;
    fork again
        :Increment APCu rate limit cache <b>(SYNC)</b>;
    end fork
endif
stop
@enduml
```
# 🔁 Event-loop Diagram

The following is a simple explanation on how ReactPHP handles I/O tasks, such as APIs or db queries, that will block code execution (asynchronous task)

Resource: https://blog.gougousis.net/the-inner-workings-of-an-event-loop-the-reactphp-case/
