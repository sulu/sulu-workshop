#!/bin/bash
set -e

git fetch origin

git checkout assignment/01
git reset --hard origin/assignment/01
git rebase --onto origin/master HEAD^1

git checkout assignment/02
git reset --hard origin/assignment/02
git rebase --onto assignment/01 HEAD^1

git checkout assignment/03
git reset --hard origin/assignment/03
git rebase --onto assignment/02 HEAD^1

git checkout assignment/04
git reset --hard origin/assignment/04
git rebase --onto assignment/03 HEAD^1

git checkout assignment/05
git reset --hard origin/assignment/05
git rebase --onto assignment/04 HEAD^1

git checkout assignment/06
git reset --hard origin/assignment/06
git rebase --onto assignment/05 HEAD^1

git checkout assignment/07
git reset --hard origin/assignment/07
git rebase --onto assignment/06 HEAD^1

git checkout assignment/08
git reset --hard origin/assignment/08
git rebase --onto assignment/07 HEAD^1

git checkout assignment/09
git reset --hard origin/assignment/09
git rebase --onto assignment/08 HEAD^1

git checkout assignment/10
git reset --hard origin/assignment/10
git rebase --onto assignment/09 HEAD^1

git checkout assignment/11
git reset --hard origin/assignment/11
git rebase --onto assignment/10 HEAD^1

git checkout assignment/12
git reset --hard origin/assignment/12
git rebase --onto assignment/11 HEAD^1

echo
git --no-pager log --oneline -n 13

echo
echo Are the commits okay? If yes, execute the following command to push them:
echo git push -f origin assignment/01 assignment/02 assignment/03 assignment/04 assignment/05 assignment/06 assignment/07 assignment/08 assignment/09 assignment/10 assignment/11 assignment/12
