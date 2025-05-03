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

function createUserEmojiPicker(inputField) {
    const emojiPicker = document.createElement('div');
    emojiPicker.className = 'user-emoji-picker';
    emojiPicker.style.cssText = 'display: none; position: absolute;'; // Set initial styles

   
    const emojiContent = document.createElement('div');
    emojiContent.className = 'user-emoji-content';

   
    const section = document.createElement('div');
    section.className = 'user-emoji-section';

    
    Object.values(emojiCategories).forEach(categoryEmojis => {
        categoryEmojis.forEach(emoji => {
            const span = document.createElement('span');
            span.textContent = emoji;
            span.onclick = () => {
                inputField.value += emoji;
                inputField.focus();
                hideEmojiPicker(emojiPicker);
            };
            section.appendChild(span);
        });
    });

    emojiContent.appendChild(section);
    emojiPicker.appendChild(emojiContent);

    
    const style = document.createElement('style');
    style.textContent = `
        .user-emoji-picker {
            position: absolute !important;
            bottom: 100% !important;
            left: 30px !important;
            background: white !important;
            border-radius: 10px !important;
            box-shadow: 0 0 10px rgba(0,0,0,0.1) !important;
            width: 350px !important;
            height: 400px !important;
            flex-direction: column !important;
            z-index: 1000 !important;
            margin-bottom: 10px !important;
        }

        .user-emoji-content {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .user-emoji-section {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 5px;
        }

        .user-emoji-section span {
            display: flex;
            aspect-ratio: 1;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.2s ease;
        }

        .user-emoji-section span:hover {
            background: #f0f2f5;
        }

        .user-emoji-content::-webkit-scrollbar {
            width: 6px;
        }

        .user-emoji-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .user-emoji-content::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
    `;
    document.head.appendChild(style);

    return emojiPicker;
}

function showEmojiPicker(picker) {
    picker.style.cssText = 'display: flex !important; position: absolute !important;';
}

function hideEmojiPicker(picker) {
    picker.style.cssText = 'display: none !important; position: absolute !important;';
}


document.addEventListener('DOMContentLoaded', () => {
    const messageInput = document.querySelector('.input-field');
    const emojiButton = document.querySelector('.emoji-button');
    const typingArea = document.querySelector('.typing-area');
    
    if (messageInput && emojiButton && typingArea) {
        const emojiPicker = createUserEmojiPicker(messageInput);
        typingArea.insertBefore(emojiPicker, messageInput);

        emojiButton.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (emojiPicker.style.display === 'none') {
                showEmojiPicker(emojiPicker);
            } else {
                hideEmojiPicker(emojiPicker);
            }
        };

        
        document.addEventListener('click', (e) => {
            if (!emojiPicker.contains(e.target) && !emojiButton.contains(e.target)) {
                hideEmojiPicker(emojiPicker);
            }
        });

        
        emojiPicker.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
}); 