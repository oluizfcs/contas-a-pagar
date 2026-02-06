.PHONY: start stop reset server db logs

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