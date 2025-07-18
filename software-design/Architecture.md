# 🌲 PlantUML Diagram

## Image:

[![](https://img.plantuml.biz/plantuml/svg/TLFBKjmm4BphA-QMtIf2xciWR2L5GbLICP1yW5OUhrNeOJGIOV-U6GastaDyagMUxfuUwPgoJhbufrHTgH0pWiCnAu2jSp1YDXFI6U2BmJd361DGZkOHjlqjGJIc9FXhymGQkEIYNFqyFDpz0CBqZEdYKxa27GRPzmTwShRkzDzx699bmBb2FXLHnSoFp02mkH5D65FqJDh4y2LZ2jg9QbTHrMYo-odNN_bOvCQ6C9NTkOqVg4gzbvj9XprAERlbldO8UKAOKtosW_WGWXCleFVQXkRe6sOmhX2Ru70q5r1NdLVJcpxEnMdsqzGIKd6P8CVQAMDyjm2T4lq7_OS3P3-C8p4vH-Bc1ypQEde_fhBpDY_EtoXQzh3gVptyNZEuvRdjlvGsFYaZiZ504Z_ECakdr4cYdAKKr0mlUOHWmqUFFgO36CqtGbfDcXErGJGNSrHsXDKz2cXO27nUI-01LWUaTKlr7WURq6JnpGgO4hkRcRHDUFCr2YEOw6U7uWsV2X9RHKS8gn0Rp_7YrABRmEtdHMOww-w8Mmpr3MqIUiwvnVFFRkkoErwBAi8BE_93YREwvhKycrS0)](https://editor.plantuml.com/uml/TLFBKjmm4BphA-QMtIf2xciWR2L5GbLICP1yW5OUhrNeOJGIOV-U6GastaDyagMUxfuUwPgoJhbufrHTgH0pWiCnAu2jSp1YDXFI6U2BmJd361DGZkOHjlqjGJIc9FXhymGQkEIYNFqyFDpz0CBqZEdYKxa27GRPzmTwShRkzDzx699bmBb2FXLHnSoFp02mkH5D65FqJDh4y2LZ2jg9QbTHrMYo-odNN_bOvCQ6C9NTkOqVg4gzbvj9XprAERlbldO8UKAOKtosW_WGWXCleFVQXkRe6sOmhX2Ru70q5r1NdLVJcpxEnMdsqzGIKd6P8CVQAMDyjm2T4lq7_OS3P3-C8p4vH-Bc1ypQEde_fhBpDY_EtoXQzh3gVptyNZEuvRdjlvGsFYaZiZ504Z_ECakdr4cYdAKKr0mlUOHWmqUFFgO36CqtGbfDcXErGJGNSrHsXDKz2cXO27nUI-01LWUaTKlr7WURq6JnpGgO4hkRcRHDUFCr2YEOw6U7uWsV2X9RHKS8gn0Rp_7YrABRmEtdHMOww-w8Mmpr3MqIUiwvnVFFRkkoErwBAi8BE_93YREwvhKycrS0)

## Code:
```plantuml
@startuml

start
note left
  All fetches, except for stock APIs occur with a 
  <u>MySQL server</u> and <u>mysqli</u> PHP driver.
end note
fork
    :Fetch from a stock external API;
    note right
        <u>Finnhub.io's</u> stock exchange API will be the provider.
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

