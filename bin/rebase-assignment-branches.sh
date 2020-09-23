#!/bin/bash
set -e

git fetch origin

git checkout assignment/01
git reset --hard origin/assignment/01
git rebase origin/master

git checkout assignment/02
git reset --hard origin/assignment/02
git rebase assignment/01

git checkout assignment/03
git reset --hard origin/assignment/03
git rebase assignment/02

git checkout assignment/04
git reset --hard origin/assignment/04
git rebase assignment/03

git checkout assignment/05
git reset --hard origin/assignment/05
git rebase assignment/04

git checkout assignment/06
git reset --hard origin/assignment/06
git rebase assignment/05

git checkout assignment/07
git reset --hard origin/assignment/07
git rebase assignment/06

git checkout assignment/08
git reset --hard origin/assignment/08
git rebase assignment/07

git checkout assignment/09
git reset --hard origin/assignment/09
git rebase assignment/08

git checkout assignment/10
git reset --hard origin/assignment/10
git rebase assignment/09

git checkout assignment/11
git reset --hard origin/assignment/11
git rebase assignment/10

git checkout assignment/12
git reset --hard origin/assignment/12
git rebase assignment/11

echo
git --no-pager log --oneline -n 13

echo
echo Are the commits okay? If yes, execute the following command to push them:
echo git push -f origin assignment/01 assignment/02 assignment/03 assignment/04 assignment/05 assignment/06 assignment/07 assignment/08 assignment/09 assignment/10 assignment/11 assignment/12
