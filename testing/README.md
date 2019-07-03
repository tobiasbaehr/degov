# Approving BackstopJS screenshots

If you intent to modify the [BackstopJS](https://github.com/garris/BackstopJS) screenshots, then please do that via the Bitbucket pipeline. Since the font rendering can differ on Linux and Mac. You can accomplish that by the following steps:

1. Open the `acceptance_test.sh` file
2. Uncomment the "set -e" line in the acceptance_test.sh file.
3. Comment in the command lines near `echo "### Approving changes"` and `### Running BackstopJS`.
4. Then download the updated BackstopJS screenshot sets via the Bitbucket Pipelines artifacts.
5. Check the HTML report inside the `backstopjs` folder, for making sure that the screenshots have been correctly approved and reflect the desired result.
6. If the HTML report is providing you the expected and successful tests: add them to your Git feature branch.