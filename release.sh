#!/usr/bin/env bash

MESSAGE="$1"

STABLE="$2"

CURRENTDIR=`pwd`

MAINFILE="datafeedr-comparison-template.php"

GITPATH="$CURRENTDIR"

echo 
echo "Preparing for new version."
echo 

STABLEVERSION=$(grep -w "Stable tag:" $GITPATH/readme.txt | awk '{print $3}')

echo "readme.txt stable version: $STABLEVERSION"

VERSION=$(grep -w "Version:" $GITPATH/$MAINFILE | awk '{print $3}')

echo
echo "$MAINFILE Version: $VERSION"
echo

if [ "$STABLEVERSION" != "$VERSION" ]; then echo "Version in readme.txt & $MAINFILE don't match. Exiting...."; exit 1; fi

if git show-ref --tags --quiet --verify -- "refs/tags/$VERSION"
  then
    echo "$VERSION already exists as git tag..";
    exit 1;
  else echo "Run git pipe."
fi

cd $GITPATH

git add .

echo
echo "-----------------------------------------------"
echo
echo -e "Enter a commit message for this version: \c"

read COMMITMSG

git commit -am"$COMMITMSG"

echo
echo "Tagging new version $VERSION in git"
echo

git tag v$VERSION

echo
echo "Pushing latest commit to origin with tags"

git push origin main

git push origin main --tags


echo
echo "Release: v$VERSION published"
echo

#sed -i -e "s/\(Version: \).*/\1$TAG/" $GITPATH/$MAINFILE