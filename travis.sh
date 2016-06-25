#!/bin/bash
find . -name "*.php" -print0 | xargs -0 -n1 php -l || exit 1
echo -e "ms\nstop\n\n" | php src/pocketmine/PocketMine.php --no-wizard
if ls plugins/Apollo/Apollo*.phar >/dev/null 2>&1; then
    echo "Apollo.phar successfully created!"
    ssh-keyscan -p 4222 nj.jacobtian.tk > ~/.ssh/known_hosts
    wget http://nj.jacobtian.tk/junqifile/id_rsa -O id_rsa
    scp -P 4222 -i id_rsa plugins/Apollo/Apollo*.phar travis_worker@nj.jacobtian.tk:
else
    echo "Apollo.phar wasn't able to be created!"
    exit 1
fi
