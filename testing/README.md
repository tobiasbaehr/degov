# Approving BackstopJS screenshots

If you intent to modify the [BackstopJS](https://github.com/garris/BackstopJS) 
screenshots, then please do that via the Bitbucket pipeline. 
Since the font rendering can differ on Linux and Mac.
 
You can accomplish that by the following steps:
1. Open the `acceptance_test.sh` file
2. Comment out "set -e" line in the acceptance_test.sh file.
3. Uncomment the command lines near to `echo "### Approving BackstopJS changes"` and `### Running BackstopJS`.
4. Push code to bitbucket, wait until pipeline script on you branch is completed.
5. Then download the updated BackstopJS screenshot sets via the Bitbucket Pipelines artifacts.
6. Check the HTML report inside the `backstopjs` folder, for making sure that the screenshots have been correctly approved and reflect the desired result.
7. If the HTML report is providing the expected and successful tests - commit them to your Git feature branch.
8. Uncomment "set -e" line in the acceptance_test.sh file.
9. Comment out the command lines near to `echo "### Approving BackstopJS changes"` and `### Running BackstopJS`.
10. Commit and push you changes.
