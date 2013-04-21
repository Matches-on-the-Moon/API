#!/bin/bash

url='cs2340api'

echo Base URL: $url

echo "== item =="

echo "add 1 item: "
curl -X PUT $url/item -d "ownerID=1&name=itemName&location=place&reward=itemReward&type=itemType&category=itemCategory&description=itemDescription"
echo -e "\n"

echo "add 1 item: "
curl -X PUT $url/item -d "ownerID=1&name=abcd&location=place&reward=ijkl&type=mnop&category=qrst&description=uvwxyz"
echo -e "\n"

echo "add 1 item: "
curl -X PUT $url/item -d "ownerID=1&name=aaaa&location=somewhere&reward=aaaa&type=aaaa&category=aaaa&description=aaaa"
echo -e "\n"

echo "get all items: "
curl -X GET $url/item/all
echo -e "\n"

# database
mysql='/usr/local/mysql/bin/mysql'
ids=`$mysql -h localhost --user=cs2340 --password=cs2340 --database=cs2340 << END
SELECT id FROM items ORDER BY id;
END`

echo -e "get last 3 inserted items:\n"
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "get item id="$id
	curl -X GET $url/item?id=$id
	echo -e "\n"
done

echo "edit item id="$id
curl -X POST $url/item -d "id=$id&ownerID=1&name=bbbb&location=somewhere&reward=bbbb&type=bbbb&category=bbbb&description=bbbb"
echo -e "\n"

echo "get item id="$id
curl -X GET $url/item?id=$id
echo -e "\n"

echo "get items matching: name = bbbb"
curl -X GET $url/item/matches?name=bbbb
echo -e "\n"

echo "get items matching: name = b"
curl -X GET $url/item/matches?name=b
echo -e "\n"

echo "get items matching: name = aaaa"
curl -X GET $url/item/matches?name=aaaa
echo -e "\n"

echo "get items matching: location = aaaa"
curl -X GET $url/item/matches?location=aaaa
echo -e "\n"

echo "get items matching: location = place"
curl -X GET $url/item/matches?location=place
echo -e "\n"

echo "get items matching: location = somewhere"
curl -X GET $url/item/matches?location=somewhere
echo -e "\n"

echo "get items matching: name = bbbb, location = somewhere"
curl -X GET "$url/item/matches?name=bbbb&location=somewhere"
echo -e "\n"

echo "get items matching: name = aaaa, location = place"
curl -X GET "$url/item/matches?name=aaaa&location=place"
echo -e "\n"

echo -e "delete last 3 inserted items:\n"
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "delete item id="$id
	curl -X DELETE $url/item -d "id=$id"
	echo -e "\n"
done

echo -e "get last 3 inserted items:\n"
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "get item id="$id
	curl -X GET $url/item?id=$id
	echo -e "\n"
done