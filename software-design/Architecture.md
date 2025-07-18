# 🌲 PlantUML Diagram

## Image:

[![](https://img.plantuml.biz/plantuml/svg/TPJ9Zjim38RlVWhUaW2ptECqdQ15sm6wfBCyW2pJiH1PyaXK9dxxadAISGxrIR2uVFpvsm-HLA3KsQB8fy9vGh3OK06miHOQ9DrYl04yQkm96XyWajTxs6mVRy1hdGAy6sf10QUiqlhNyFpt9qGC1moh-xG6vMfvtmtnpPhyPljZ2tKm771N8DzAqwAOmMzfhYgV2AX5wRM7UM-LnXfCrs5j5A4Te4A-GZ2q67c3sRK2_79EShx6o5WyG4WEkcJ9z1Q1LDn7M5LhxHs31tHaXzLzjURa58tR2UWJAasCAAYp27ZWAB3Uzq0UbBN-dUkxsygo2fA2HzI9Z7VJOGI3Ym8iluc2q0JViKIZT7Wa34vPqR3CKLTZZ0-JDCQvDbLtnizY5kwShblbTYZfuv0LPi7wu0-c5bMbm0KcIw9sohYHw3iIQ9iYG_2g5IV4CgDdw6AvzNsohFIfNS38-aMPNXR7GLqv2Rgkz9_eL_O30t5S5ChUHvw-HbB6nlDrJ5LdQ79_AZ5Q2URRFyyl2muU1TrilwJHZP8M8-iFGNYiuIfbSUqC5bCIieYVs95Wt6s7dGy3QCK6br63OacDavxJJPe6vcA9batuqU3pGXHtC1ymBaPPdzYV0JLbcpZ049YkvwBZYfTVlLG4xJkseh3XMyB8g6WZmjpviSx5EJdfqV7uDD5CaIelSPEjdg9rm4xyckNvm1sdB2--o4tuO1h-xdrVFF1P_WB_0000)](https://editor.plantuml.com/uml/TPJ9Zjim38RlVWhUaW2ptECqdQ15sm6wfBCyW2pJiH1PyaXK9dxxadAISGxrIR2uVFpvsm-HLA3KsQB8fy9vGh3OK06miHOQ9DrYl04yQkm96XyWajTxs6mVRy1hdGAy6sf10QUiqlhNyFpt9qGC1moh-xG6vMfvtmtnpPhyPljZ2tKm771N8DzAqwAOmMzfhYgV2AX5wRM7UM-LnXfCrs5j5A4Te4A-GZ2q67c3sRK2_79EShx6o5WyG4WEkcJ9z1Q1LDn7M5LhxHs31tHaXzLzjURa58tR2UWJAasCAAYp27ZWAB3Uzq0UbBN-dUkxsygo2fA2HzI9Z7VJOGI3Ym8iluc2q0JViKIZT7Wa34vPqR3CKLTZZ0-JDCQvDbLtnizY5kwShblbTYZfuv0LPi7wu0-c5bMbm0KcIw9sohYHw3iIQ9iYG_2g5IV4CgDdw6AvzNsohFIfNS38-aMPNXR7GLqv2Rgkz9_eL_O30t5S5ChUHvw-HbB6nlDrJ5LdQ79_AZ5Q2URRFyyl2muU1TrilwJHZP8M8-iFGNYiuIfbSUqC5bCIieYVs95Wt6s7dGy3QCK6br63OacDavxJJPe6vcA9batuqU3pGXHtC1ymBaPPdzYV0JLbcpZ049YkvwBZYfTVlLG4xJkseh3XMyB8g6WZmjpviSx5EJdfqV7uDD5CaIelSPEjdg9rm4xyckNvm1sdB2--o4tuO1h-xdrVFF1P_WB_0000)

## Code:
```plantuml
@startuml

start
note left
  All fetches, except for stock API, occur with a 
  <u>MySQL server</u> and <u>mysqli</u> PHP driver.
end note

' Note about the fork (placed immediately before it)
note right
  The fork nodes will run multiple tasks <b>concurrently</b>
  using <u>ReactPHP</u>'s event loop to allow non-blocking execution.
end note

fork
    :Fetch from a stock external API;
    note right
        <u>finnhub.io's</u> stock exchange API will be the provider.
    end note
fork again
    :Get cluster leaders;
end fork

:Populate stock results to PHP form;
:Populate cluster leader results to PHP form;
:User enters proposal details;
:User submits proposal to PHP server (POST);
:Fetch APCu for submission rate limit;

note right
  <u>APCu</u> = an in-memory cache storage in PHP.
end note

if (Reached rate limit?) then (yes)
    :Redirect to an error page;
    :Do not complete request;
else (no)
    fork
        :Insert proposal data;
    fork again
        :Increment APCu rate limit cache;
    end fork
endif
stop
@enduml
```
# Event-loop Diagram

The following is a simple explanation on how ReactPHP handles I/O tasks, such as APIs or db queries, that will block code execution (asynchronous task)

