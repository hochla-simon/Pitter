file <- "./data/English_dependency_tree_metrics.txt";
dataRead = read.table(file,header=FALSE)
numberRows <- dim(dataRead)[1]
#Checking validity:
validity <- TRUE;
for( i in 1:numberRows){
  n = dataRead[i,1];
  meanKsquare = dataRead[i,2];
  meanD = dataRead[i,3];
  if(!((4-6/n)<=meanKsquare && meanKsquare<=(n-1) )){
    validity <- FALSE;
    break;
  }
  dLowerBound = n/(8*(n-1))*meanKsquare + 1/2;
  dUpperBound = n-1;
  if(!(dLowerBound<=meanD && meanD<=dUpperBound )){
    validity <- FALSE;
    break;
  }
}
if(validity){
  cat("The data in file ",file," is valid");
}else{
  cat("The data in file ",file," is unvalid");
}