import React from 'react';
import ScrollStack, { ScrollStackItem } from './ScrollStack';

const ProcessSectionStack = ({ items }) => {
  return (
    <ScrollStack 
      useWindowScroll={true} 
      itemDistance={40} 
      itemStackDistance={30}
      stackPosition="220px"
      baseScale={0.9}
      itemScale={0.03}
      blurAmount={0}
    >
      {items.map((item, index) => (
        <ScrollStackItem key={index}>
            <div className="p-8 rounded-xl border border-gray-100 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 transition-colors group section-glow bg-transparent dark:bg-[#111111]/50 h-full flex flex-col justify-center">
                <div className="w-10 h-10 rounded border border-gray-200 dark:border-white/15 flex items-center justify-center mb-6 transition-colors">
                    <i className={`fas ${item.icon} text-gray-400 dark:text-gray-500 group-hover:text-gray-800 dark:group-hover:text-yellow-400 transition-colors`}></i>
                </div>
                <h3 className="font-medium text-gray-900 dark:text-white mb-3 tracking-wide">{item.title}</h3>
                <p className="text-sm text-gray-500 dark:text-gray-400 font-light leading-relaxed">{item.desc}</p>
            </div>
        </ScrollStackItem>
      ))}
    </ScrollStack>
  );
};

export default ProcessSectionStack;
