import sys

def main():
    data = list(map(int, sys.stdin.read().split()))
    if not data: return
    n = data[0]
    positions = data[1:1 + n]
    B = data[1 + n]
    F = data[2 + n]
    intervals = sorted((p + B, p - F) for p in positions)
    ans = 0
    i = 0
    while i < n:
        ans += 1
        cover_until = intervals[i][0]
        while i < n and intervals[i][1] <= cover_until:
            i += 1
    print(ans)
if __name__ == "__main__":
    main()
