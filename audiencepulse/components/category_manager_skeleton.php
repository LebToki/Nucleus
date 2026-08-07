<?php
/**
 * Content Management Controller View Component
 * This file simulates the UI component for managing content categories (CRUD).
 */

// Note: In a real MVC, this would be included by a base view template.
function renderCategoryManager() {
    $categories = \App\Services\CategoryManagementService::getAllCategories(); 
    ?>
    <div class="WOWDASH-card" style="grid-column: span 2;">
        <h3 class="WOWDASH-header">📂 Content Governance & Categories</h3>
        <p class="WOWDASH-subtitle">Manage the metadata that powers content grouping and analytics. (Service: CategoryManagementService)</p>

        <!-- Create Form -->
        <div class="card-form mb-4 p-3 border rounded bg-light">
            <h5>➕ Add New Category</h5>
            <form onsubmit="event.preventDefault(); alert('Category creation logic simulated.');">
                <input type="text" placeholder="Category Name (e.g., Viral)" required class="form-control me-2">
                <textarea placeholder="Description" class="form-control me-2"></textarea>
                <button type="submit" class="btn btn-success WOWDASH-btn">Create Category</button>
            </form>
        </div>

        <!-- Read/List View (Table) -->
        <div class="table-responsive">
            <table class="table table-striped WOWDASH-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Total Events Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr data-id="<?= htmlspecialchars($cat['id']) ?>">
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= htmlspecialchars($cat['description']) ?></td>
                            <td><?= number_format($cat['count']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-info WOWDASH-btn">View Details</button>
                                <button class="btn btn-sm btn-warning WOWDASH-btn">Edit</button>
                                <button class="btn btn-sm btn-danger WOWDASH-btn" onclick="if(confirm('Are you sure?')) { /* Call delete logic */ }">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

// Note: This function is meant to be included by DashboardController.php
?>