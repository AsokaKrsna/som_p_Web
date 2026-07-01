import sys

def solve():
    d = list(map(int, sys.stdin.read().split()))
    if not d: return
    
    n = d[0]
    p = sorted(d[1:n+1])
    B, F = d[n+1], d[n+2]
    
    cnt = 0
    end = -float('inf')
    reach = B + F
    
    for x in p:
        if x > end:
            cnt += 1
            end = x + reach
            
    print(cnt)

solve()
