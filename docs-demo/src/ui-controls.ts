import { focusToObjectPosition } from './focus-engine';
import { images } from './image-data';

let currentIndex = 0;
let customFocus: { x: number; y: number } | null = null;

const $ = <T extends HTMLElement>(selector: string) =>
    document.querySelector<T>(selector)!;

export function initUI(): void {
    renderImageSelector();
    renderDemo();
    bindControls();
    addCopyButtons();
}

function addCopyButtons(): void {
    document.querySelectorAll<HTMLElement>('.code-block').forEach((block) => {
        const button = document.createElement('button');
        button.className = 'copy-btn';
        button.textContent = 'Copy';
        button.addEventListener('click', () => {
            const code = block.querySelector('code');
            if (!code) return;
            navigator.clipboard.writeText(code.textContent ?? '').then(() => {
                button.textContent = 'Copied!';
                setTimeout(() => { button.textContent = 'Copy'; }, 1500);
            });
        });
        block.style.position = 'relative';
        block.appendChild(button);
    });
}

function renderImageSelector(): void {
    const nav = $('#image-nav');

    images.forEach((image, index) => {
        const button = document.createElement('button');
        button.className = `image-btn${index === 0 ? ' active' : ''}`;
        button.dataset.index = String(index);
        button.textContent = image.label;
        nav.appendChild(button);
    });

    nav.addEventListener('click', (event) => {
        const button = (event.target as HTMLElement).closest<HTMLButtonElement>('.image-btn');
        if (!button) return;
        currentIndex = Number(button.dataset.index);
        customFocus = null;
        nav.querySelectorAll('.image-btn').forEach((element) => element.classList.remove('active'));
        button.classList.add('active');
        renderDemo();
    });
}

function renderDemo(): void {
    const image = images[currentIndex];
    const focus = customFocus ?? image.focus;

    const focusedElement = $('#img-focused') as HTMLImageElement;
    focusedElement.src = image.src;
    focusedElement.alt = image.alt;
    focusedElement.style.objectPosition = focusToObjectPosition(focus.x, focus.y);

    const defaultElement = $('#img-default') as HTMLImageElement;
    defaultElement.src = image.src;
    defaultElement.alt = image.alt;
    defaultElement.style.objectPosition = '50% 50%';

    const fullElement = $('#img-full') as HTMLImageElement;
    fullElement.src = image.src;
    fullElement.alt = image.alt;

    const dot = $('#focus-dot');
    dot.style.left = `${focus.x * 100}%`;
    dot.style.top = `${focus.y * 100}%`;

    $('#css-output').textContent =
        `object-fit: cover;\nobject-position: ${focusToObjectPosition(focus.x, focus.y)};`;

    $('#focus-values').textContent =
        `focus: { x: ${focus.x.toFixed(2)}, y: ${focus.y.toFixed(2)} }`;
}

function bindControls(): void {
    const slider = $<HTMLInputElement>('#width-slider');
    const containers = document.querySelectorAll<HTMLElement>('.comparison-img');
    slider.addEventListener('input', () => {
        const width = slider.value;
        containers.forEach((container) => (container.style.width = `${width}%`));
        $('#width-label').textContent = `${width}%`;
    });

    const fullContainer = $('#full-image-container');
    fullContainer.addEventListener('click', (event) => {
        const rect = fullContainer.getBoundingClientRect();
        customFocus = {
            x: (event.clientX - rect.left) / rect.width,
            y: (event.clientY - rect.top) / rect.height,
        };
        renderDemo();
    });
}
