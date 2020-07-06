### Symfony 5 application capable of consuming queued jobs stored in a database table, using parallel workers.
#### Using:
- Symfony Messenger Component, 
- Custom Transport Bus 
- [Supervisor](http://supervisord.org/)


#### How to test:
1. To generate 100 valid URLS: http://proxify.local/generate?url=http://google.com&times=100
 
2. To generate 100 invalid URLS: http://proxify.local/generate?url=http://invalid&times=100

3. To see job status: 
    http://proxify.local/status
    or
    http://localhost:9001  | user / 123
    
    
4. Generate as many other sets of URLS as you wish and see the jobs getting done!
