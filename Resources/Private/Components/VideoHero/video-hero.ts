/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Connetation Elements – Video Hero
 *
 * Two independent concerns, both optional depending on playback mode:
 * - Click-to-play: swaps the poster for a real, sound-on, user-controlled
 *   player once the visitor clicks play.
 * - Background loop (YouTube/Vimeo only): fades the iframe in only once
 *   it's actually loaded, instead of showing it immediately — masks the
 *   brief flash of the provider's own player chrome (e.g. YouTube's
 *   controls) that can appear before its `controls=0` etc. params take
 *   effect. Self-hosted <video> doesn't need this — its autoplay/muted
 *   attributes apply immediately, there's no equivalent third-party chrome
 *   to flash.
 */

class VideoHero {

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const playButton = this.el.querySelector<HTMLButtonElement>('.conn-video-hero__play');
        playButton?.addEventListener('click', () => this.play());

        const bgIframe = this.el.querySelector<HTMLIFrameElement>('.conn-video-hero__bg-iframe');
        bgIframe?.addEventListener('load', () => bgIframe.classList.add('conn-video-hero__bg-iframe--ready'));
    }

    private play(): void {
        const mediaType = this.el.dataset.mediaType ?? 'file';
        const mediaUrl = this.el.dataset.mediaUrl ?? '';
        const mediaMime = this.el.dataset.mediaMime ?? '';
        const videoId = this.el.dataset.videoId ?? '';

        const player = document.createElement(mediaType === 'file' ? 'video' : 'iframe');
        player.className = 'conn-video-hero__player';

        if (mediaType === 'file') {
            const video = player as HTMLVideoElement;
            video.controls = true;
            video.autoplay = true;
            video.playsInline = true;
            const source = document.createElement('source');
            source.src = mediaUrl;
            source.type = mediaMime;
            video.appendChild(source);
        } else {
            const iframe = player as HTMLIFrameElement;
            iframe.allow = 'autoplay; fullscreen; encrypted-media';
            iframe.allowFullscreen = true;
            iframe.src = mediaType === 'youtube'
                ? `https://www.youtube.com/embed/${videoId}?autoplay=1`
                : `https://player.vimeo.com/video/${videoId}?autoplay=1`;
        }

        this.el.querySelectorAll('.conn-video-hero__play, .conn-video-hero__tag, .conn-video-hero__duration, .conn-video-hero__inner')
            .forEach((node) => node.remove());
        this.el.classList.add('conn-video-hero--playing');
        this.el.appendChild(player);
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-video-hero', (el) => new VideoHero(el));
