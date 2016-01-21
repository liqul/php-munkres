# php-munkres
A php implementation of the Munkres's Algorithm for task assignment. 

Please refer to http://csclab.murraystate.edu/bob.pilgrim/445/munkres.html for more details about the algorithm. 

To use this code, simply new the MukresAlgorithm object, initialize the cost matix, and invoke the runMunkres function. Done :)

###Some practices:

In general, the Munkres's algorithm deals with task assignment problems where the number of tasks N equals the number of workers M. However, in practice, this may not be true. In the case where N < M, the typical work around is inject N-M dummy tasks with high costs for each worker. Handing the case where N > M is more complicated depending more on domain knowledge. There are usually two solutions. The first is to do the assignment in multiple rounds where in each round we have N <= M. However, one need to carefully determine the order of task assignment carefully. The other is to group the N tasks into N'<=M clusters and calculate the costs between task clusters and workers. To my opinion, the latter sounds more reasonable.
