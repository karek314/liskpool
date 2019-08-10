FILE_NAME=$1
cp gen-2017/$FILE_NAME 1.json
cp legacy/$FILE_NAME 2.json
cp $FILE_NAME 3.json
mv $FILE_NAME "backup_$FILE_NAME"
cat 1.json 2.json 3.json > tmp.json && tr -d "\n\r" < tmp.json >> $FILE_NAME && rm tmp.json 1.json 2.json 3.json && sed -i 's/\]\[/,/g' $FILE_NAME
php chart.php $FILE_NAME