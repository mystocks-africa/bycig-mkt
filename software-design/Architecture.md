# 🌲 PlantUML Diagram

## Image:

[![](https://img.plantuml.biz/plantuml/svg/TLJBRjim4BpxA_OM6t2IU-AwCLAq3T27cyUXHufQMuGfKk4Zjlw-ivITEcu368P4xSxEpExo9YOLKcxjQ5IUHiud9ikhD29QM4ihJhhXE2NUQUuIhNoWcBpUq69vDoMlTGwqDQaXHKYPvVdt_k7NDueSNZZCBlESbAlblEtZipNbPFbrINKm2BWOCRvAqT5GEfXr8xKV6vPQ6tohEQ82k8JigCqscSuo9HKtaMRLN7i73e5TiltiifeZEKVZrbBpdfLEg2PLpoBn2wB8UjzHygIizLlWk_FAGf2ay8vrJiQxTtd15KS0HKoHRHpXBnQQf9KZnY47gbZggzWxNIJ3DhmVNYyA4iWlXfDgljUZ0hzbLFNaLCeutZO6if7p9eLAv15nAj1FCKDUFvLuTL8ZgGrQxDqQD673aCfJYXvIkAUjpxQcsiIGqV2ysqC-YapvjPqFIS78YhNAaSaaGezqC1KwrS3JW-r3sr9WbH1qxAcqjg1UVPO1erNmBIPec2pU9GvEMHan4J1UFFp-SJiH2TSbwQGZnSiyNnddcbnT67yMY-S7DDqeY2reXH1w0w7K1VzYQfb10NZbLgPEhPLn0y4ld4ZR7C49Yw6G4FyX9Sb5qkXgwRji5VWDrGD7J6mKeyKG1BNNHq5lWT-9Vi8oWL-IoY3T-GXlQaxAs7Zu77FLcdJqVGynx26DbpyV7YS87knUB6_piCgI5gDCSn0-rW3b-cJcuQqa54y_O9t9kFEMMnzwJ3nk1v4Q51p6Cg3cSP_DYiOo3GtNHmK-JgG1ZiOznydWyZtN9h1EPJ6nHI60NGVGeUDNdxmWalOjbb-uyNFc2AfiCO5ZvmUSrxagINSEyjEHPogf_mxKIS_toJfmAnT6SUil-xremJc1UHq3F9WLRbJVZMxmZFlr3m00)](https://editor.plantuml.com/uml/TLJBRjim4BpxA_OM6t2IU-AwCLAq3T27cyUXHufQMuGfKk4Zjlw-ivITEcu368P4xSxEpExo9YOLKcxjQ5IUHiud9ikhD29QM4ihJhhXE2NUQUuIhNoWcBpUq69vDoMlTGwqDQaXHKYPvVdt_k7NDueSNZZCBlESbAlblEtZipNbPFbrINKm2BWOCRvAqT5GEfXr8xKV6vPQ6tohEQ82k8JigCqscSuo9HKtaMRLN7i73e5TiltiifeZEKVZrbBpdfLEg2PLpoBn2wB8UjzHygIizLlWk_FAGf2ay8vrJiQxTtd15KS0HKoHRHpXBnQQf9KZnY47gbZggzWxNIJ3DhmVNYyA4iWlXfDgljUZ0hzbLFNaLCeutZO6if7p9eLAv15nAj1FCKDUFvLuTL8ZgGrQxDqQD673aCfJYXvIkAUjpxQcsiIGqV2ysqC-YapvjPqFIS78YhNAaSaaGezqC1KwrS3JW-r3sr9WbH1qxAcqjg1UVPO1erNmBIPec2pU9GvEMHan4J1UFFp-SJiH2TSbwQGZnSiyNnddcbnT67yMY-S7DDqeY2reXH1w0w7K1VzYQfb10NZbLgPEhPLn0y4ld4ZR7C49Yw6G4FyX9Sb5qkXgwRji5VWDrGD7J6mKeyKG1BNNHq5lWT-9Vi8oWL-IoY3T-GXlQaxAs7Zu77FLcdJqVGynx26DbpyV7YS87knUB6_piCgI5gDCSn0-rW3b-cJcuQqa54y_O9t9kFEMMnzwJ3nk1v4Q51p6Cg3cSP_DYiOo3GtNHmK-JgG1ZiOznydWyZtN9h1EPJ6nHI60NGVGeUDNdxmWalOjbb-uyNFc2AfiCO5ZvmUSrxagINSEyjEHPogf_mxKIS_toJfmAnT6SUil-xremJc1UHq3F9WLRbJVZMxmZFlr3m00)

## Code:
```plantuml
@startuml

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
        <u>finnhub.io's</u> stock exchange API will be the provider.
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
  <u>APCu</u> = an in-memory cache storage in PHP.
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
# Event-loop Diagram

The following is a simple explanation on how ReactPHP handles I/O tasks, such as APIs or db queries, that will block code execution (asynchronous task)

