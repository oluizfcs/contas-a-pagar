.PHONY: start stop reset server db logs cert

start:
	docker compose up --build -w

stop:
	docker compose down

reset:
	docker compose down -v
	docker compose up --build -w

server:
	docker exec -it contas-a-pagar-server-1 bash

db:
	docker exec -it contas-a-pagar-db-1 bash

logs:
	docker compose logs -f

cert:
	openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
		-keyout ./app/config/apache/ca.key \
		-out ./app/config/apache/ca.crt \
		-config ./app/config/apache/openssl.conf -subj "/CN=Minha CA Raiz"

	openssl req -nodes -newkey rsa:2048 \
		-keyout ./app/config/apache/server.key \
		-out ./app/config/apache/server.csr \
		-subj "/CN=<HOST>"

	openssl x509 -req -in ./app/config/apache/server.csr \
		-CA ./app/config/apache/ca.crt -CAkey ./app/config/apache/ca.key -CAcreateserial \
		-out ./app/config/apache/server.crt -days 3650 \
		-extfile ./app/config/apache/openssl.conf -extensions v3_req