
<script>
    function formatBytes(bytes) {
        const units = ['B','KB','MB','GB','TB'];
        let i = 0;
        while (bytes >= 1024 && i < units.length-1) {
            bytes /= 1024;
            i++;
        }
        return bytes.toFixed(2) + ' ' + units[i];
    }

    document.addEventListener("DOMContentLoaded", () => {
        fetch('/summary/data')
            .then(res => res.json())
            .then(json => {
                if (!json.success) return;

                const data = json.data;

                const used = data.totalStorageUsed;
                const max = data.maxCapacity;

                const mbUsed = (used / (1024 * 1024)).toFixed(2);
                const gbMax = (max / (1024 ** 3)).toFixed(0);

                const percent = ((used / max) * 100).toFixed(2);

                document.getElementById("storageFooter").innerHTML = `
                    <div>${mbUsed} MB of ${gbMax} GB</div>
                    <div class="progress mt-1" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: ${percent}%"></div>
                    </div>
                `;
            })
            .catch(() => {
                document.getElementById("storageFooter").innerHTML = "Failed to load";
            });
    });

  </script>