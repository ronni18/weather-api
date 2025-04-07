down-all:
	#Parando Proyecto Principal
	@docker-compose down --remove-orphans

start-all:
	#Subiendo Proyecto Principal
	@docker-compose up -d
