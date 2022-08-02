Nous lançons pour cela un Pod basé sur l'image mingrammer/flog afin de générer 1000 entrées de log:

$ kubectl run testlog --restart=Never --image=mingrammer/flog -- -f apache_combined

En utilisant la commande suivante, récupérez ensuite les logs qui ont été générés:

$ kubectl logs testlog > nginx.log

Utilisez ensuite la commande suivante pour envoyer chaque ligne à Logstash (assurez vous de remplacer HOST par l'adresse IP de l'une des machines du cluster):

Si vous êtes sur Linux / MacOS


while read -r line; do curl -s -XPUT -d "$line" http://HOST:31500; done < ./nginx.log