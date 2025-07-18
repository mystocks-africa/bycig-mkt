# 🫡 UML Diagram
In Software Engineering, UML or Unified Modelling Language is a standardized graphical language that allows developers to visualize the structure and behaviour of software systems, making it easier for a developers to implement their software systems in code. There are multiple different types of visualizations for a developer to choose.

The proposal submission workflow is documented using a UML Activity Graph

## Image:

[![](https://img.plantuml.biz/plantuml/svg/TLHDJzj04BtxLyoDHA9m1veIKRL5waTAuD3ZUZs9LrdlclqW-D_tpJg1a1OfYkprp9lttipiTKmgfDpPgaecMQP5yBsFoj8orvs9qNX7IxzAMnMugafqvNnYihnA5T7SMbfnqYt7K-9dpNsYbGyKazSRcYzkJybhdGDjJMf94LAkykp7iFpzdIA79mvNvtb6oZLotWtnqPfoilYse2OO19nLZAzIj1fB1xDkfVPzor9hWsyDHrG0bv0TTTacqqDCKd4JwQgUQU_08R1BThWwhsT8pj6ujTIyOwKJgadLaqZyX2YotlUKF2bh_HRuxaDj8KXI-9brJl3bNLvmnH50nJkHRHpXBnQQf9KZnY47gbdggpWuNIJ3DhplNyyA4iZFnvDwjjE3JhnbL0_aLCeuthO6if7p9eLAv05nAj0FCKFUS2hnwgX6KXkqsBirQCA6CPLFANf8uO6sFjk66XD3HiFpymxoNcJAh-jzI0f6rgnLZamc4NgaWwdHgHQUxcqVsvO2guIWGq-bjGNrueiC6As2xp11usJnS-BWCASOCH4mcI___BoPYeJBadJKaU9bdgsCSsskpum_YSNpFPfk5SGMj488lO5GweD_CetCe02yS2jJfzRAk97WLqwaROxWXCLGI8Z_a9Ba8gcwMFW-MmL-O_N04HCRnMWn147TvK7GM-1teX-mJE2Nf7A_t-66ap8sxZz7MUbqy7q7CUuXJHQ_blTJ18zspnStULpbrviW21zhW79vDFFmLXAAfn-npcJSXuux7mPCF6u7aHeK7COoeEPXdysA9Z8DBJS71JvDfG6E9WF7wUZo7JSci4vbCR558O1T3z2nunUVlI2IzXsMNxZnO-O8gcmnWHFdHvoNkIf9jmxoqu5dAgd_3jHHptV9Ed0d5qPnwvNzJhJW7C6yZ04Up0etgk-hQppZ3luB)](https://editor.plantuml.com/uml/TLHDJzj04BtxLyoDHA9m1veIKRL5waTAuD3ZUZs9LrdlclqW-D_tpJg1a1OfYkprp9lttipiTKmgfDpPgaecMQP5yBsFoj8orvs9qNX7IxzAMnMugafqvNnYihnA5T7SMbfnqYt7K-9dpNsYbGyKazSRcYzkJybhdGDjJMf94LAkykp7iFpzdIA79mvNvtb6oZLotWtnqPfoilYse2OO19nLZAzIj1fB1xDkfVPzor9hWsyDHrG0bv0TTTacqqDCKd4JwQgUQU_08R1BThWwhsT8pj6ujTIyOwKJgadLaqZyX2YotlUKF2bh_HRuxaDj8KXI-9brJl3bNLvmnH50nJkHRHpXBnQQf9KZnY47gbdggpWuNIJ3DhplNyyA4iZFnvDwjjE3JhnbL0_aLCeuthO6if7p9eLAv05nAj0FCKFUS2hnwgX6KXkqsBirQCA6CPLFANf8uO6sFjk66XD3HiFpymxoNcJAh-jzI0f6rgnLZamc4NgaWwdHgHQUxcqVsvO2guIWGq-bjGNrueiC6As2xp11usJnS-BWCASOCH4mcI___BoPYeJBadJKaU9bdgsCSsskpum_YSNpFPfk5SGMj488lO5GweD_CetCe02yS2jJfzRAk97WLqwaROxWXCLGI8Z_a9Ba8gcwMFW-MmL-O_N04HCRnMWn147TvK7GM-1teX-mJE2Nf7A_t-66ap8sxZz7MUbqy7q7CUuXJHQ_blTJ18zspnStULpbrviW21zhW79vDFFmLXAAfn-npcJSXuux7mPCF6u7aHeK7COoeEPXdysA9Z8DBJS71JvDfG6E9WF7wUZo7JSci4vbCR558O1T3z2nunUVlI2IzXsMNxZnO-O8gcmnWHFdHvoNkIf9jmxoqu5dAgd_3jHHptV9Ed0d5qPnwvNzJhJW7C6yZ04Up0etgk-hQppZ3luB)

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
# 🔁 Event-loop Diagram

The following is a simple explanation on how ReactPHP handles I/O tasks, such as APIs or db queries, that will block code execution (asynchronous task)

