#!/bin/bash

TARGET_USER="toluwa"
REPO_FOLDER="Homelabs002/Enterprise-Linux-Labs"
BRANCH="main"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Auto switch to the correct user if needed
if [ "$USER" != "$TARGET_USER"] && [ "$(id -u)" -ne "$(id -us "$TARGET_USER")" ]; then
	echo -e "${YELLOW} Switch to $TARGET_USER...${NC}"
	echo sudo -u "$TARGET_USER" bash "$0" "$@"
	exit
fi

echo -e "${GREEN}Pushgin $REPO_FOLDER to Github as $TARGET_USER${NC}"
echo

# Go to the repo folder
cd ~/"$REPO_FOLDER" || {
	echo -e "${RED}ERROR ~/$REPO_FOLDER not found ${NC}"
	exit
}

# Ask for commit
while true; do
	read -p "Commit message: " commit_msg
	[ -n "$commit_msg" ] && break
	echo -e "${YELLOW}Message cannot be empty!${NC}"
done

echo
git add .
git commit -m "$commit_msg" || echo -e "${YELLOW} No changes to cmooit${NC}"
echo -e "${GREEN}Pushing to origin, branch = $BRANCH...${NC}"
git push origin "$BRANCH"

[ $? -eq 0 ] && echo -e "\n${GREEN}All done! Successfully pushed${NC}" || echo -e "\n${RED}Push Failed${NC}"
