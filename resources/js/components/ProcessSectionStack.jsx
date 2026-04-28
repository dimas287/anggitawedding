import React from 'react';
import ScrollStack, { ScrollStackItem } from './ScrollStack';

// Helper to chunk array into pairs
const chunkArray = (array, size) => {
  const chunked = [];
  for (let i = 0; i < array.length; i += size) {
    chunked.push(array.slice(i, i + size));
  }
  return chunked;
};

const ProcessSectionStack = ({ items }) => {
  const itemPairs = chunkArray(items, 2);

  return (
    <ScrollStack 
      useWindowScroll={true} 
      itemDistance={40} 
      itemStackDistance={30}
      stackPosition="20%"
      baseScale={0.9}
      itemScale={0.03}
      blurAmount={0}
    >
      {itemPairs.map((pair, index) => (
        <ScrollStackItem key={index}>
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full">
            {pair.map((item, idx) => (
              <div key={idx} className="p-8 rounded-2xl border border-gray-100 dark:border-white/10 hover:border-gray-300 dark:hover:border-white/20 transition-all group bg-white dark:bg-[#111111] shadow-lg dark:shadow-none h-full flex flex-col justify-center">
                  <div className="w-12 h-12 rounded-lg border border-gray-200 dark:border-white/15 flex items-center justify-center mb-6 transition-colors">
                      <i className={`fas ${item.icon} text-lg text-gray-400 dark:text-gray-500 group-hover:text-gray-800 dark:group-hover:text-yellow-400 transition-colors`}></i>
                  </div>
                  <h3 className="font-medium text-lg text-gray-900 dark:text-white mb-3 tracking-wide">{item.title}</h3>
                  <p className="text-sm text-gray-500 dark:text-gray-400 font-light leading-relaxed">{item.desc}</p>
              </div>
            ))}
          </div>
        </ScrollStackItem>
      ))}
    </ScrollStack>
  );
};

export default ProcessSectionStack;
