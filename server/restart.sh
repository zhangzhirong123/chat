echo "loading"
pid=`pidof chat-master`
echo $pid
kill -USR1 $pid
echo "loading success"
