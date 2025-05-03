const emojiCategories = {
    'Smileys & People': [
        '😀', '😃', '😄', '😁', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', 
        '😗', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏', '😒', '😞',
        '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬',
        '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗', '🤔', '🤭', '🤫', '🤥', '😶', '😐'
    ],
    'Animals & Nature': [
        '🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🐔',
        '🦄', '🐝', '🦋', '🐌', '🐞', '🐜', '🦗', '🕷️', '🦂', '🐢', '🐍', '🦎', '🦖', '🦕', '🐙', '🦑',
        '🌺', '🌸', '🌼', '🌻', '🌹', '🥀', '🌷', '🌱', '🌲', '🌳', '🌴', '🌵', '🌾', '🌿', '☘️', '🍀'
    ],
    'Food & Drink': [
        '🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅',
        '🥨', '🥖', '🧀', '🍖', '🍗', '🥩', '🥓', '🍔', '🍟', '🍕', '🌭', '🥪', '🌮', '🌯', '🥙', '🥚',
        '🍿', '🧂', '🥤', '🧃', '🧉', '🧊', '🥢', '🍽️', '🍴', '🥄', '🔪', '🍳', '🥣', '🥡', '🥢', '🧊'
    ],
    'Activities': [
        '⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱', '🪀', '🏓', '🏸', '🏒', '🏑', '🥍',
        '🎮', '🎲', '🎭', '🎨', '🎬', '🎤', '🎧', '🎼', '🎹', '🥁', '🎷', '🎺', '🎸', '🪕', '🎻', '🎲'
    ],
    'Travel & Places': [
        '🚗', '🚕', '🚙', '🚌', '🚎', '🏎️', '🚓', '🚑', '🚒', '🚐', '🛻', '🚚', '🚛', '🚜', '🛴', '🚲',
        '✈️', '🛫', '🛬', '🛩️', '💺', '🚀', '🛸', '🚁', '🛶', '⛵', '🚤', '🛥️', '🛳️', '⛴️', '🚢', '⚓'
    ],
    'Objects': [
        '⌚', '📱', '💻', '⌨️', '🖥️', '🖨️', '🖱️', '🖲️', '🕹️', '🗜️', '💽', '💾', '💿', '📀', '📼', '📷',
        '🔋', '🔌', '💡', '🔦', '🕯️', '🪔', '🧯', '🛢️', '💸', '💵', '💴', '💶', '💷', '💰', '💳', '💎'
    ],
    'Symbols': [
        '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖',
        '✨', '💫', '⭐', '🌟', '✡️', '☪️', '✝️', '☮️', '☯️', '☢️', '☣️', '📛', '⚜️', '🔱', '⚕️', '♻️'
    ]
};

function createEmojiPicker(inputField) {
    const emojiPicker = document.createElement('div');
    emojiPicker.className = 'emoji-picker';

    // Create emoji content area
    const emojiContent = document.createElement('div');
    emojiContent.className = 'emoji-content';

    // Add all emojis in a single section
    const section = document.createElement('div');
    section.className = 'emoji-section active';

    // Combine all emojis from different categories
    Object.values(emojiCategories).forEach(categoryEmojis => {
        categoryEmojis.forEach(emoji => {
            const span = document.createElement('span');
            span.textContent = emoji;
            span.onclick = () => {
                inputField.value += emoji;
                inputField.focus();
                // Hide picker after selecting emoji
                emojiPicker.classList.remove('active');
            };
            section.appendChild(span);
        });
    });

    emojiContent.appendChild(section);
    emojiPicker.appendChild(emojiContent);

    // Add styles
    const style = document.createElement('style');
    style.textContent = `
        .emoji-picker {
            position: absolute;
            bottom: 80px;
            left: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 350px;
            height: 400px;
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
        }

        .emoji-picker.active {
            display: flex;
        }

        .emoji-content {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }

        .emoji-section {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 5px;
        }

        .emoji-section span {
            display: flex;
            aspect-ratio: 1;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.2s ease;
        }

        .emoji-section span:hover {
            background: #f0f2f5;
        }

        .emoji-content::-webkit-scrollbar {
            width: 6px;
        }

        .emoji-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .emoji-content::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
    `;
    document.head.appendChild(style);

    return emojiPicker;
}

// Update the click outside handler to use the active class
document.addEventListener('click', (e) => {
    const emojiPickers = document.querySelectorAll('.emoji-picker');
    emojiPickers.forEach(picker => {
        if (!picker.contains(e.target) && !e.target.classList.contains('emoji-button')) {
            picker.classList.remove('active');
        }
    });
}); 