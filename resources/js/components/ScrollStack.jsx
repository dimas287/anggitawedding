import { useLayoutEffect, useRef, useCallback } from 'react';
import Lenis from 'lenis';
import './ScrollStack.css';

export const ScrollStackItem = ({ children, itemClassName = '' }) => (
  <div className="scroll-stack-card-wrapper">
    <div className={`scroll-stack-card ${itemClassName}`.trim()}>{children}</div>
  </div>
);

const ScrollStack = ({
  children,
  className = '',
  itemDistance = 100,
  itemScale = 0.03,
  itemStackDistance = 30,
  stackPosition = '20%',
  scaleEndPosition = '10%',
  baseScale = 0.85,
  scaleDuration = 0.5,
  rotationAmount = 0,
  blurAmount = 0,
  useWindowScroll = false,
  onStackComplete
}) => {
  const scrollerRef = useRef(null);
  const stackCompletedRef = useRef(false);
  const animationFrameRef = useRef(null);
  const lenisRef = useRef(null);
  const cardsRef = useRef([]);
  const wrappersRef = useRef([]);
  const lastTransformsRef = useRef(new Map());
  const isUpdatingRef = useRef(false);
  const nativeScrollRef = useRef(null);

  const calculateProgress = useCallback((scrollTop, start, end) => {
    if (scrollTop < start) return 0;
    if (scrollTop > end) return 1;
    return (scrollTop - start) / (end - start);
  }, []);

  const parsePercentage = useCallback((value, containerHeight) => {
    if (typeof value === 'string' && value.includes('%')) {
      return (parseFloat(value) / 100) * containerHeight;
    }
    return parseFloat(value);
  }, []);

  const getScrollData = useCallback(() => {
    if (useWindowScroll) {
      return {
        scrollTop: window.scrollY,
        containerHeight: window.innerHeight,
        scrollContainer: document.documentElement
      };
    } else {
      const scroller = scrollerRef.current;
      return {
        scrollTop: scroller.scrollTop,
        containerHeight: scroller.clientHeight,
        scrollContainer: scroller
      };
    }
  }, [useWindowScroll]);

  const getElementOffset = useCallback(
    element => {
      if (useWindowScroll) {
        const rect = element.getBoundingClientRect();
        return rect.top + window.scrollY;
      } else {
        return element.offsetTop;
      }
    },
    [useWindowScroll]
  );

  // Desktop-only JS transform update — NOT used on mobile
  const updateCardTransforms = useCallback(() => {
    if (!cardsRef.current.length || isUpdatingRef.current) return;
    isUpdatingRef.current = true;

    const { scrollTop, containerHeight } = getScrollData();
    const stackPositionPx = parsePercentage(stackPosition, containerHeight);
    const scaleEndPositionPx = parsePercentage(scaleEndPosition, containerHeight);

    const scrollerElement = scrollerRef.current;
    const scrollerTop = getElementOffset(scrollerElement);
    const scrollerHeight = scrollerElement.offsetHeight;
    const scrollerBottom = scrollerTop + scrollerHeight;

    wrappersRef.current.forEach((wrapper, i) => {
      const card = cardsRef.current[i];
      if (!wrapper || !card) return;

      const cardTop = getElementOffset(wrapper);
      const triggerStart = cardTop - stackPositionPx - itemStackDistance * i;
      const triggerEnd = cardTop - scaleEndPositionPx;
      const pinStart = cardTop - stackPositionPx - itemStackDistance * i;

      const cardHeight = wrapper.offsetHeight;
      const stackBottom = stackPositionPx + cardHeight + (itemStackDistance * wrappersRef.current.length);
      const pinEnd = scrollerBottom - stackBottom;

      const scaleProgress = calculateProgress(scrollTop, triggerStart, triggerEnd);
      const targetScale = baseScale + i * itemScale;
      const scale = 1 - scaleProgress * (1 - targetScale);
      const rotation = rotationAmount ? i * rotationAmount * scaleProgress : 0;

      let blur = 0;
      if (blurAmount) {
        let topCardIndex = 0;
        for (let j = 0; j < wrappersRef.current.length; j++) {
          const jCardTop = getElementOffset(wrappersRef.current[j]);
          const jTriggerStart = jCardTop - stackPositionPx - itemStackDistance * j;
          if (scrollTop >= jTriggerStart) topCardIndex = j;
        }
        if (i < topCardIndex) {
          blur = Math.max(0, (topCardIndex - i) * blurAmount);
        }
      }

      let translateY = 0;
      const isPinned = scrollTop >= pinStart && scrollTop <= pinEnd;
      if (isPinned) {
        translateY = scrollTop - cardTop + stackPositionPx + itemStackDistance * i;
      } else if (scrollTop > pinEnd) {
        translateY = pinEnd - cardTop + stackPositionPx + itemStackDistance * i;
      }

      const newTransform = {
        translateY: Math.round(translateY * 100) / 100,
        scale: Math.round(scale * 1000) / 1000,
        rotation: Math.round(rotation * 100) / 100,
        blur: Math.round(blur * 100) / 100
      };

      const lastTransform = lastTransformsRef.current.get(i);
      const hasChanged =
        !lastTransform ||
        Math.abs(lastTransform.translateY - newTransform.translateY) > 0.1 ||
        Math.abs(lastTransform.scale - newTransform.scale) > 0.001 ||
        Math.abs(lastTransform.rotation - newTransform.rotation) > 0.1 ||
        Math.abs(lastTransform.blur - newTransform.blur) > 0.1;

      if (hasChanged) {
        const transform = `translate3d(0, ${newTransform.translateY}px, 0) scale(${newTransform.scale}) rotate(${newTransform.rotation}deg)`;
        const filter = newTransform.blur > 0 ? `blur(${newTransform.blur}px)` : '';
        card.style.transform = transform;
        card.style.filter = filter;
        lastTransformsRef.current.set(i, newTransform);
      }

      if (i === cardsRef.current.length - 1) {
        const isInView = scrollTop >= pinStart && scrollTop <= pinEnd;
        if (isInView && !stackCompletedRef.current) {
          stackCompletedRef.current = true;
          onStackComplete?.();
        } else if (!isInView && stackCompletedRef.current) {
          stackCompletedRef.current = false;
        }
      }
    });

    // Synchronize the external sticky header exit animation
    const header = document.getElementById('harmoni-header');
    const isMobile = window.innerWidth < 1024 || 'ontouchstart' in window;

    // Only run header animation on desktop to avoid mobile jitter
    if (header && cardsRef.current.length > 0 && !isMobile) {
      const containerHeight = getScrollData().containerHeight;
      const stackPositionPx = parsePercentage(stackPosition, containerHeight);
      const lastCard = cardsRef.current[cardsRef.current.length - 1];
      const cardHeight = lastCard.offsetHeight;
      const scrollerElement = scrollerRef.current;
      const scrollerTop = getElementOffset(scrollerElement);
      const scrollerBottom = scrollerTop + scrollerElement.offsetHeight;
      const stackBottom = stackPositionPx + cardHeight + (itemStackDistance * cardsRef.current.length);
      const pinEnd = scrollerBottom - stackBottom;
      const { scrollTop } = getScrollData();
      if (scrollTop > pinEnd) {
        const overflow = scrollTop - pinEnd;
        header.style.transform = `translate3d(0, -${overflow}px, 0)`;
      } else {
        header.style.transform = 'translate3d(0, 0, 0)';
      }
    }

    isUpdatingRef.current = false;
  }, [
    itemScale,
    itemStackDistance,
    stackPosition,
    scaleEndPosition,
    baseScale,
    rotationAmount,
    blurAmount,
    useWindowScroll,
    onStackComplete,
    calculateProgress,
    parsePercentage,
    getScrollData,
    getElementOffset
  ]);

  const handleScroll = useCallback(() => {
    updateCardTransforms();
  }, [updateCardTransforms]);

  const setupScrolling = useCallback(() => {
    const isMobile = window.innerWidth < 1024 || 'ontouchstart' in window;

    if (isMobile && useWindowScroll) {
      // Mobile: pure CSS sticky — no JS needed at scroll time
      // The header is now static (relative) on mobile, so it will scroll out of view naturally.
      // We only need to trigger onStackComplete via an observer to notify the parent if needed.
      const lastWrapper = wrappersRef.current[wrappersRef.current.length - 1];
      if (lastWrapper && onStackComplete) {
        const observer = new IntersectionObserver(
          ([entry]) => {
            if (entry.isIntersecting && !stackCompletedRef.current) {
              stackCompletedRef.current = true;
              onStackComplete();
            } else if (!entry.isIntersecting && stackCompletedRef.current) {
              stackCompletedRef.current = false;
            }
          },
          { threshold: 0.5 }
        );
        observer.observe(lastWrapper);
        nativeScrollRef.current = () => observer.disconnect();
      }
      return null;
    }

    if (useWindowScroll) {
      const lenis = new Lenis({
        duration: 1.2,
        easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
        infinite: false,
        wheelMultiplier: 1,
        lerp: 0.1
      });
      lenis.on('scroll', handleScroll);
      const raf = time => {
        lenis.raf(time);
        animationFrameRef.current = requestAnimationFrame(raf);
      };
      animationFrameRef.current = requestAnimationFrame(raf);
      lenisRef.current = lenis;
      return lenis;
    } else {
      const scroller = scrollerRef.current;
      if (!scroller) return;
      const lenis = new Lenis({
        wrapper: scroller,
        content: scroller.querySelector('.scroll-stack-inner'),
        duration: 1.2,
        easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
        infinite: false,
        normalizeWheel: true,
        wheelMultiplier: 1,
        lerp: 0.1
      });
      lenis.on('scroll', handleScroll);
      const raf = time => {
        lenis.raf(time);
        animationFrameRef.current = requestAnimationFrame(raf);
      };
      animationFrameRef.current = requestAnimationFrame(raf);
      lenisRef.current = lenis;
      return lenis;
    }
  }, [handleScroll, useWindowScroll, onStackComplete]);

  useLayoutEffect(() => {
    const scroller = scrollerRef.current;
    if (!scroller) return;

    const isMobile = window.innerWidth < 1024 || 'ontouchstart' in window;

    const cards = Array.from(
      useWindowScroll
        ? document.querySelectorAll('.scroll-stack-card')
        : scroller.querySelectorAll('.scroll-stack-card')
    );
    const wrappers = Array.from(
      useWindowScroll
        ? document.querySelectorAll('.scroll-stack-card-wrapper')
        : scroller.querySelectorAll('.scroll-stack-card-wrapper')
    );

    cardsRef.current = cards;
    wrappersRef.current = wrappers;
    const transformsCache = lastTransformsRef.current;

    const containerHeight = useWindowScroll ? window.innerHeight : scroller.clientHeight;
    const stackPositionPx = parsePercentage(stackPosition, containerHeight);

    if (isMobile && useWindowScroll) {
      // ---- MOBILE: Pure CSS sticky, no JS transforms ----
      // Each wrapper becomes sticky at its own top offset
      // so they stack up naturally as user scrolls
      wrappers.forEach((wrapper, i) => {
        // Offset starts near the top since the header scrolls away natively on mobile
        const topOffset = 24 + itemStackDistance * i;
        wrapper.style.position = 'sticky';
        wrapper.style.top = `${topOffset}px`;
        wrapper.style.zIndex = `${40 + i}`;
        wrapper.style.marginBottom = '0px';
      });

      // Add a spacer to the end of the inner container to provide scroll height
      const inner = scroller.querySelector('.scroll-stack-inner');
      if (inner && !inner.querySelector('.mobile-scroll-spacer')) {
        const spacer = document.createElement('div');
        spacer.className = 'mobile-scroll-spacer';
        spacer.style.height = `${containerHeight * 0.7}px`; // Provide scroll room
        inner.appendChild(spacer);
      }
      // Cards: just reset any leftover transform state
      cards.forEach(card => {
        card.style.willChange = 'auto';
        card.style.transform = 'none';
        card.style.filter = 'none';
      });
    } else {
      // ---- DESKTOP: JS margin + transform approach ----
      wrappers.forEach((wrapper, i) => {
        wrapper.style.position = '';
        wrapper.style.top = '';
        wrapper.style.zIndex = `${i + 1}`;
        if (i < wrappers.length - 1) {
          const pinPos = stackPositionPx + itemStackDistance * i;
          const calculatedMargin = containerHeight - pinPos - wrapper.offsetHeight;
          const margin = Math.max(itemDistance, calculatedMargin);
          wrapper.style.marginBottom = `${margin}px`;
        }
      });
      cards.forEach((card, i) => {
        card.style.willChange = 'transform, filter';
        card.style.transformOrigin = 'top center';
        card.style.backfaceVisibility = 'hidden';
        card.style.transform = 'translateZ(0)';
        card.style.webkitTransform = 'translateZ(0)';
        card.style.perspective = '1000px';
        card.style.webkitPerspective = '1000px';
      });
    }

    setupScrolling();

    if (!isMobile || !useWindowScroll) {
      updateCardTransforms();
    }

    return () => {
      if (animationFrameRef.current) cancelAnimationFrame(animationFrameRef.current);
      if (lenisRef.current) lenisRef.current.destroy();
      if (nativeScrollRef.current) {
        nativeScrollRef.current(); // calls observer.disconnect()
        nativeScrollRef.current = null;
      }
      stackCompletedRef.current = false;
      cardsRef.current = [];
      transformsCache.clear();
      isUpdatingRef.current = false;
    };
  }, [
    itemDistance,
    itemScale,
    itemStackDistance,
    stackPosition,
    scaleEndPosition,
    baseScale,
    scaleDuration,
    rotationAmount,
    blurAmount,
    useWindowScroll,
    onStackComplete,
    setupScrolling,
    updateCardTransforms,
    parsePercentage
  ]);

  return (
    <div className={`scroll-stack-scroller ${className}`.trim()} ref={scrollerRef}>
      <div className="scroll-stack-inner">
        {children}
      </div>
    </div>
  );
};

export default ScrollStack;
