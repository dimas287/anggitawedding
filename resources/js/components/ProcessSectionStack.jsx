import React, { useEffect, useRef, useState } from 'react';
import ScrollStack, { ScrollStackItem } from './ScrollStack';

// Helper to chunk array into pairs
const chunkArray = (array, size) => {
  const chunked = [];
  for (let i = 0; i < array.length; i += size) {
    chunked.push(array.slice(i, i + size));
  }
  return chunked;
};

const AnimatedCard = ({ children, direction }) => {
  const ref = useRef(null);
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    const observer = new IntersectionObserver(([entry]) => {
      // Add a small delay to make the animation feel more natural during scroll
      if (entry.isIntersecting) {
        setTimeout(() => setIsVisible(true), 150);
        observer.unobserve(entry.target);
      }
    }, { threshold: 0.15 });
    
    if (ref.current) observer.observe(ref.current);
    return () => observer.disconnect();
  }, []);

  const baseClass = "transition-all duration-1000 ease-out transform h-full w-full";
  // On desktop (lg+), force cards to be fully visible and in place immediately
  const hiddenClass = direction === 'left' 
    ? "opacity-0 -translate-x-16 lg:translate-x-0 lg:opacity-100" 
    : "opacity-0 translate-x-16 lg:translate-x-0 lg:opacity-100";
  const visibleClass = "opacity-100 translate-x-0";

  return (
    <div ref={ref} className={`${baseClass} ${isVisible ? visibleClass : hiddenClass}`}>
      {children}
    </div>
  );
};

const ProcessSectionStack = ({ items }) => {
  const itemPairs = chunkArray(items, 2);
  const [isMobile, setIsMobile] = useState(false);

  useEffect(() => {
    const checkMobile = () => setIsMobile(window.innerWidth < 1024);
    checkMobile(); // Check on mount
    window.addEventListener('resize', checkMobile);
    return () => window.removeEventListener('resize', checkMobile);
  }, []);

  const getDirection = (pairIndex, cardIndex) => {
    // Global index ensures perfect alternation (Right, Left, Right, Left, Right, Left)
    const globalIndex = pairIndex * 2 + cardIndex;
    return globalIndex % 2 === 0 ? 'right' : 'left';
  };

  return (
    <ScrollStack 
      useWindowScroll={true} 
      itemDistance={40} 
      itemStackDistance={isMobile ? 15 : 30}
      stackPosition="220px"
      baseScale={0.9}
      itemScale={0.03}
      blurAmount={0}
    >
      {itemPairs.map((pair, index) => (
        <ScrollStackItem key={index}>
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-6 w-full">
            {pair.map((item, idx) => (
              <AnimatedCard key={idx} direction={getDirection(index, idx)}>
                <div className="p-8 rounded-2xl border border-gray-100 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 transition-all group bg-white dark:bg-[#111111] shadow-lg dark:shadow-none h-full flex flex-col justify-center">
                    <div className="w-12 h-12 rounded-lg border border-gray-200 dark:border-white/15 flex items-center justify-center mb-6 transition-colors">
                        <i className={`fas ${item.icon} text-lg text-gray-400 dark:text-gray-500 group-hover:text-gray-800 dark:group-hover:text-yellow-400 transition-colors`}></i>
                    </div>
                    <h3 className="font-medium text-lg text-gray-900 dark:text-white mb-3 tracking-wide">{item.title}</h3>
                    <p className="text-sm text-gray-500 dark:text-gray-400 font-light leading-relaxed">{item.desc}</p>
                </div>
              </AnimatedCard>
            ))}
          </div>
        </ScrollStackItem>
      ))}
    </ScrollStack>
  );
};

export default ProcessSectionStack;
