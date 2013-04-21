#!/bin/bash

url='cs2340api'

echo Base URL: $url

echo "== account =="

echo "create 1 account: "
curl -X PUT $url/account -d "loginName=loginName&password=password&name=name&email=email"
echo -e "\n"

echo "create 1 account: "
curl -X PUT $url/account -d "loginName=aaaa&password=aaaa&name=aaaa&email=aaaa"
echo -e "\n"

echo "create 1 account: "
curl -X PUT $url/account -d "loginName=bbbb&password=bbbb&name=bbbb&email=bbbb"
echo -e "\n"

echo "get all accounts: "
curl -X GET $url/account/all
echo -e "\n"

# database
mysql='/usr/local/mysql/bin/mysql'
ids=`$mysql -h localhost --user=cs2340 --password=cs2340 --database=cs2340 << END
SELECT id FROM accounts ORDER BY id;
END`

echo -e "get last 3 inserted accounts:\n"
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "get account id="$id
	curl -X GET $url/account?id=$id
	echo -e "\n"
done

echo "login account 1: "
curl -X GET "$url/account/attemptLogin?loginName=loginName&password=password"
echo -e "\n"

echo "login account 2: "
curl -X GET "$url/account/attemptLogin?loginName=aaaa&password=aaaa"
echo -e "\n"

echo "login account 3: "
curl -X GET "$url/account/attemptLogin?loginName=bbbb&password=bbbb"
echo -e "\n"



echo "get login state for account 1: "
curl -X GET "$url/account/stateForLoginName?loginName=loginName"
echo -e "\n"

echo "get login state for account 2: "
curl -X GET "$url/account/stateForLoginName?loginName=aaaa"
echo -e "\n"

echo "get login state for account 3: "
curl -X GET "$url/account/stateForLoginName?loginName=bbbb"
echo -e "\n"

for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "lock account id="$id": "
	curl -X POST $url/account/lock -d "id="$id
	echo -e "\n"
done


echo "get login state for account 1: "
curl -X GET "$url/account/stateForLoginName?loginName=loginName"
echo -e "\n"

echo "get login state for account 2: "
curl -X GET "$url/account/stateForLoginName?loginName=aaaa"
echo -e "\n"

echo "get login state for account 3: "
curl -X GET "$url/account/stateForLoginName?loginName=bbbb"
echo -e "\n"

for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "unlock account id="$id": "
	curl -X POST $url/account/unlock -d "id="$id
	echo -e "\n"
done


echo "get login state for account 1: "
curl -X GET "$url/account/stateForLoginName?loginName=loginName"
echo -e "\n"

echo "get login state for account 2: "
curl -X GET "$url/account/stateForLoginName?loginName=aaaa"
echo -e "\n"

echo "get login state for account 3: "
curl -X GET "$url/account/stateForLoginName?loginName=bbbb"
echo -e "\n"


for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "change password for account id="$id
	curl -X POST $url/account/editPassword -d "id=$id&password=newPassword"
	echo -e "\n"
done
echo -e "get last 3 inserted accounts:\n"
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "get account id="$id
	curl -X GET $url/account?id=$id
	echo -e "\n"
done

for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "change email for account id="$id
	curl -X POST $url/account/editEmail -d "id=$id&email=newEmail"
	echo -e "\n"
done
echo -e "get last 3 inserted accounts:\n"
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "get account id="$id
	curl -X GET $url/account?id=$id
	echo -e "\n"
done

echo "check unique name, expect unique: "
curl -X GET "$url/account/isLoginNameUnique?loginName=cccc"
echo -e "\n"

echo "check unique name, expect not unique: "
curl -X GET "$url/account/isLoginNameUnique?loginName=aaaa"
echo -e "\n"


echo "get id for account 1: "
curl -X GET "$url/account/idByLoginName?loginName=loginName"
echo -e "\n"

echo "get id for account 2: "
curl -X GET "$url/account/idByLoginName?loginName=aaaa"
echo -e "\n"

echo "get id for account 3: "
curl -X GET "$url/account/idByLoginName?loginName=bbbb"
echo -e "\n"


for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "is admin account id="$id
	curl -X GET "$url/account/isAdmin?id=$id"
	echo -e "\n"
done
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "promote account id="$id
	curl -X POST $url/account/promote -d "id=$id"
	echo -e "\n"
done
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "is admin account id="$id
	curl -X GET "$url/account/isAdmin?id=$id"
	echo -e "\n"
done


echo "lock account by wrong passwords"

echo "login account 1 attempt 1, wrong attempt: "
curl -X GET "$url/account/attemptLogin?loginName=loginName&password=wrongPassword"
echo ""
echo "get login state for account 1: "
curl -X GET "$url/account/stateForLoginName?loginName=loginName"
echo ""
echo "login account 1 attempt 2, wrong attempt: "
curl -X GET "$url/account/attemptLogin?loginName=loginName&password=wrongPassword"
echo ""
echo "get login state for account 1: "
curl -X GET "$url/account/stateForLoginName?loginName=loginName"
echo ""
echo "login account 1 attempt 3, wrong attempt: "
curl -X GET "$url/account/attemptLogin?loginName=loginName&password=wrongPassword"
echo ""
echo "get login state for account 1: "
curl -X GET "$url/account/stateForLoginName?loginName=loginName"
echo -e "\n"


for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "delete account id="$id": "
	curl -X DELETE $url/account -d "id="$id
	echo -e "\n"
done
echo -e "get last 3 inserted accounts:\n"
for id in `echo $ids | tr ' ' '\n' | tail -3`
do
	echo "get account id="$id
	curl -X GET $url/account?id=$id
	echo -e "\n"
done