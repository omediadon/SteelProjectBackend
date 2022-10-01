## Create with open ssh

- Create a private key using `openssl genrsa -aes256 -out private.pem 2048`
- Use that key to generate a public copy for it using `openssl rsa -pubout -in private.pem -out public.pem`