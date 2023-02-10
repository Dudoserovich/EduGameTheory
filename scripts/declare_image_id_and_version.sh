sudo set -xe

IMAGE_ID=$REGISTRY/$IMAGE_NAME

# Change all uppercase to lowercase
IMAGE_ID=$(echo $IMAGE_ID | tr '[A-Z]' '[a-z]')

# Strip git ref prefix from version
VERSION=$(echo "$GITHUB_SHA" | sed -e 's,.*/\(.*\),\1,')

echo "IMAGE_ID=$IMAGE_ID" >> $GITHUB_ENV
echo "VERSION=$VERSION" >> $GITHUB_ENV
