import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: Readonly<SVGAttributes<SVGElement>>) {
    return (
        <svg
            {...props}
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 144 144"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            fillRule="evenodd"
            clipRule="evenodd"
            className="lucide"
        >
            <path
                id="Background"
                d="M142.259,37.741l0,68.518c0,19.869 -16.131,36 -36,36l-68.518,0c-19.869,0 -36,-16.131 -36,-36l0,-68.518c0,-19.869 16.131,-36 36,-36l68.518,0c19.869,0 36,16.131 36,36Z"
                style={{ fill: '#09090b' }}
            />
            <g id="X">
                <path
                    d="M90.226,88.296l31.735,42.671l-12.437,0l-25.731,-34.308l6.432,-8.363l0,0Zm19.298,-75.263l12.437,-0l-31.95,42.886l-6.004,-8.577l25.517,-34.308Z"
                    style={{ fill: '#858ea2', fillRule: 'nonzero' }}
                />
                <path
                    d="M34.476,13.033l43.743,58.967l-43.743,58.967l-12.437,0l43.742,-58.967l-43.742,-58.967l12.437,0Z"
                    style={{ fill: '#fa0019', fillRule: 'nonzero' }}
                />
            </g>
        </svg>
    );
}
