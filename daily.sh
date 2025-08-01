#!/bin/bash
# Создание директории
if [ ! -d "download" ]; then
    mkdir "download"
fi
# Переменные
nowdate=$(date -u +%d-%m-%Y)
file="$(ls "download" | grep "^[0-9]*\.zip$" | shuf -n 1)"
# Проверка и копирование файла
if [ -f download/$file ]; then
    cp "download/$file" "download/daily.zip"
    echo "[$nowdate] New daily game is '$file'" >> dailygame.log
else
    echo "[$nowdate] Impossible to find a new game. Check your game folder!" >> dailygame.log
fi
echo "===================" >> dailygame.log