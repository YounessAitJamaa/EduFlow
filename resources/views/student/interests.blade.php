@extends('layouts.base')

@section('title', 'Manage Your Interests')

@section('content')
<div class="interests-container" style="max-width: 800px; margin: 40px auto;">
    <div class="text-center" style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">Customize Your Experience</h1>
        <p style="color: var(--gray); font-size: 1.1rem;">Select the topics you're passionate about to get better course recommendations.</p>
    </div>

    <div id="loadingInterests" class="text-center" style="padding: 50px 0;">
        <div class="spinner"></div>
    </div>

    <div id="interestsGrid" style="display: none;">
        <div class="interest-list" id="interestList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; margin-bottom: 40px;">
            <!-- Interests will be loaded here -->
        </div>

        <div class="text-center">
            <button id="saveInterestsBtn" class="btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">Save Preferences</button>
            <p style="margin-top: 20px;"><a href="/dashboard" style="color: var(--gray); text-decoration: none;">Skip for now</a></p>
        </div>
    </div>
</div>

<style>
    .interest-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
    }
    .interest-card:hover {
        border-color: var(--primary);
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.1);
    }
    .interest-card.selected {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }
    .interest-card i {
        font-size: 1.5rem;
        margin-bottom: 10px;
        display: block;
    }
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid rgba(79, 70, 229, 0.1);
        border-left-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('scripts')
<script>
    let selectedInterests = new Set();

    async function loadInterests() {
        try {
            // Load all available interests
            const allInterestsResponse = await api.get('/interests');
            const allInterests = allInterestsResponse.interests;

            // Load student's current interests
            const myInterestsResponse = await api.get('/student/interests');
            const myInterests = myInterestsResponse.interests || [];
            
            myInterests.forEach(i => selectedInterests.add(i.id));

            const list = document.getElementById('interestList');
            list.innerHTML = '';

            allInterests.forEach(interest => {
                const isSelected = selectedInterests.has(interest.id);
                const card = document.createElement('div');
                card.className = `interest-card ${isSelected ? 'selected' : ''}`;
                card.innerHTML = `
                    <i class="fas ${getIconForInterest(interest.name)}"></i>
                    <span style="font-weight: 600;">${interest.name}</span>
                `;
                card.onclick = () => toggleInterest(card, interest.id);
                list.appendChild(card);
            });

            document.getElementById('loadingInterests').style.display = 'none';
            document.getElementById('interestsGrid').style.display = 'block';

        } catch (error) {
            alert('Error loading interests: ' + error.message);
        }
    }

    function toggleInterest(element, id) {
        if (selectedInterests.has(id)) {
            selectedInterests.delete(id);
            element.classList.remove('selected');
        } else {
            selectedInterests.add(id);
            element.classList.add('selected');
        }
    }

    function getIconForInterest(name) {
        const map = {
            'PHP': 'fa-code',
            'JavaScript': 'fa-js',
            'Web Design': 'fa-paint-brush',
            'Mobile Development': 'fa-mobile-alt',
            'Data Science': 'fa-database',
            'Business': 'fa-briefcase',
            'Marketing': 'fa-bullhorn'
        };
        return map[name] || 'fa-tag';
    }

    document.getElementById('saveInterestsBtn').onclick = async () => {
        const btn = document.getElementById('saveInterestsBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        try {
            await api.post('/student/interests', {
                interest_ids: Array.from(selectedInterests)
            });
            window.location.href = '/dashboard';
        } catch (error) {
            alert('Error saving interests: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = 'Save Preferences';
        }
    };

    document.addEventListener('DOMContentLoaded', loadInterests);
</script>
@endsection
