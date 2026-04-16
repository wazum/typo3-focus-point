export interface FocusImage {
    src: string;
    alt: string;
    focus: { x: number; y: number };
    label: string;
}

export const images: FocusImage[] = [
    {
        src: 'images/lighthouse.jpg',
        alt: 'Lighthouse on rocky jetty at sunset',
        focus: { x: 0.68, y: 0.4 },
        label: 'Lighthouse — right third',
    },
    {
        src: 'images/crossing.jpg',
        alt: 'Pedestrian crossing in Lazio, Italy from above',
        focus: { x: 0.79, y: 0.17 },
        label: 'Crossing — person upper-right',
    },
    {
        src: 'images/cat.jpg',
        alt: 'Gray cat relaxing on colorful blanket',
        focus: { x: 0.77, y: 0.48 },
        label: 'Cat — face on the right',
    },
];
