Intallation
---

Run:

`php doctrine:database:create`

`php doctrine:migrations:migrate`

`mkdir var/keys`

`openssl genrsa -out var/keys/private.pem -aes256 4096`

`openssl rsa -pubout -in var/keys/private.pem -out var/keys/public.pem`

Add ssh passphrase from the step above to `JWT_PASSPHRASE` environment variable

In order to get JWT token send the following request and use response in your requests to API

`curl -X POST -H "Content-Type: application/json" http://localhost/api/login_check -d '{"username":"test_user","password":"qwerty"}'`

Swagger
---

Swagger documentation is placed at http://localhost/api/doc (where localhost is your domain name)


What is managers?
---

Managers in my terminology is the same as repositories in DDD. I had to rename them just to avoid mussing them up with doctrine repositories.
