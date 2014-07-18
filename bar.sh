#!/bin/sh


ESC='^['
#  ^[ は Ctrl+vの後 Esc を押すことで入力


#########################
# MAIN
#########################
if [ $# -eq 0 ]
then
 echo "Error: not arg"
 echo "$0 ＜LINE＞ ＜%＞ ＜Return Row＞ ＜Return Column＞"
 echo "example: $0 20 10 30 1"
 exit 1
fi
LINE=$1
PERC=$2
RETL=$3
RETC=$4

chrV="####################################################################################################"
chrS="____________________________________________________________________________________________________"
printf "${ESC}[${LINE};1H["
chrBAR=`echo "$chrV" | cut -c1-${PERC} 2>/dev/null`

if [ $PERC -ge 100 ];then
 CNT=0
else
((CNT = 100 - ${PERC}))
fi

chrSPC=`echo "$chrS" | cut -c1-${CNT} 2>/dev/null`

if [ $CNT -ne 100 ] ;then
printf "$chrBAR"
fi

if [ $CNT -ne 0 ] ;then
printf "$chrSPC"
fi

printf "] [ ${PERC}%%]"
printf "${ESC}[${RETL};${RETC}H"
#EOF
