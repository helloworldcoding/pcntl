#!/bin/env bash

for (( i=1; i<=8; i++ ))
do
	echo `date +%s%N`+"$i"
done

wait
