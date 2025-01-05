<div class="w-100">
    <div class="skeleton-grid">
        <div class="skeleton-box"></div>
        <div class="skeleton-box"></div>
        <div class="skeleton-box"></div>
        <div class="skeleton-box"></div>
        <div class="skeleton-box"></div>
        <div class="skeleton-box"></div>
    </div>
</div>

<style>
    .skeleton-grid {
        display: grid;
        grid-template-columns: repeat(3, 400px); /* Three fixed columns */
        gap: 16px; /* Space between grid items */
        justify-content: center; /* Center-align the grid within the container */
        padding: 16px;
    }

    .skeleton-box {
        width: 400px;
        height: 520px;
        background: linear-gradient(90deg, #e0e0e0 25%, #f8f8f8 50%, #e0e0e0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px; /* Optional rounded corners */
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
    }
        100% {
            background-position: 200% 0;
        }
    }

</style>
