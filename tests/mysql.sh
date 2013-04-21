#!/bin/bash

mysql='/usr/local/mysql/bin/mysql'

ids=`$mysql -h localhost --user=cs2340 --password=cs2340 --database=cs2340 << END
SELECT id FROM items;
END`
