# 🌲 PlantUML Diagram

## Image:

[![](https://img.plantuml.biz/plantuml/svg/TLAxRiCm3Dpr5HoJeJyGeq2257rCDT9swAZAT2n45bsHAf2_BsMbgJjK4-Kxdkz8RLbiaZm6CnUcftGoe6TpZ-86w1EDy6u78kXzjD4Xh5Z8dNZTp6ImH-jZRNb00HSo2oO8Q3jCt1YC7SogcvQc7AmWp0AGa7CG1Y5e7zl26Pi5wQ_GF-mtLWYZyXYcH1En3T2XM1_u1-RyCNfPu1S9nRuKNhNFBwyQvX9ujszpKQzjp9uYfE8dU5LfZ8cab-IFWsZeNRlFS0isWeyt8uwKpk2i6-QCoHvHlvUVpLCedSRu7bO7B9nk8Rm66J32weoyhiCyOESJEYbsLHzJKbEJIZOLlwCY28x6AM2nX9yPMHrYO5rIfAfpNUZSz1GrjIn6PSKsLzvYbnUsIpZgU6lENxSrOssyxbSBt-ipeibijIvlwXi0)](https://editor.plantuml.com/uml/TLAxRiCm3Dpr5HoJeJyGeq2257rCDT9swAZAT2n45bsHAf2_BsMbgJjK4-Kxdkz8RLbiaZm6CnUcftGoe6TpZ-86w1EDy6u78kXzjD4Xh5Z8dNZTp6ImH-jZRNb00HSo2oO8Q3jCt1YC7SogcvQc7AmWp0AGa7CG1Y5e7zl26Pi5wQ_GF-mtLWYZyXYcH1En3T2XM1_u1-RyCNfPu1S9nRuKNhNFBwyQvX9ujszpKQzjp9uYfE8dU5LfZ8cab-IFWsZeNRlFS0isWeyt8uwKpk2i6-QCoHvHlvUVpLCedSRu7bO7B9nk8Rm66J32weoyhiCyOESJEYbsLHzJKbEJIZOLlwCY28x6AM2nX9yPMHrYO5rIfAfpNUZSz1GrjIn6PSKsLzvYbnUsIpZgU6lENxSrOssyxbSBt-ipeibijIvlwXi0)

## Code:
```plantuml
@startuml
start
fork
    :Fetch from Yahoo finance (stocks);
fork again
    :Get cluster leaders;
end fork
:Populate stock results to PHP form;
:Populate cluster leader results to PHP form;
:User enters proposal details;
:User submits proposal to PHP server (POST);
:Fetch ACPu for submission rate limit;

note right
  APCu = an in-memory cache storage in PHP
end note

if (Reached rate limit) then (yes)
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
