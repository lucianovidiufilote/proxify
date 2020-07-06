### How to test:
To generate 100 valid URLS:
    http://proxify.local/generate?url=http://google.com&times=100
 
To generate 100 invalid URLS:
    http://proxify.local/generate?url=http://invalid&times=100

sudo systemctl start supervisor

To see job status: 
    http://proxify.local/status
    http://localhost:9001/
    user / 123
    
Then generate other sets of URLS and see the jobs getting done!


### PREREQUISITES:
supervisor
symfony 5.1
symfony messenger component
custom transport bus
